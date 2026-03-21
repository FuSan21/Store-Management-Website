<?php

namespace App\Support\Cart;

use App\Enums\ShippingMethod;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * One cart API for both guests (kept in the session) and signed-in
 * customers (kept in the carts table). A guest cart is merged into the
 * customer's cart when they sign in.
 */
class CartService
{
    private const SESSION_ITEMS = 'cart.items';

    private const SESSION_COUPON = 'cart.coupon';

    /** @var Collection<int, CartLine>|null */
    private ?Collection $lines = null;

    public function __construct(
        private readonly Session $session,
        private readonly AuthFactory $auth,
    ) {}

    /**
     * @return Collection<int, CartLine>
     */
    public function lines(): Collection
    {
        if ($this->lines !== null) {
            return $this->lines;
        }

        $quantities = $this->quantities();

        $products = Product::whereIn('bmuk_no', array_keys($quantities))->get()->keyBy('bmuk_no');

        return $this->lines = collect($quantities)
            ->filter(fn ($quantity, $bmukNo) => $products->has($bmukNo))
            ->map(fn ($quantity, $bmukNo) => new CartLine($products[$bmukNo], (int) $quantity))
            ->values();
    }

    public function isEmpty(): bool
    {
        return $this->lines()->isEmpty();
    }

    public function count(): int
    {
        return (int) array_sum($this->quantities());
    }

    public function quantityOf(int $bmukNo): int
    {
        return (int) ($this->quantities()[$bmukNo] ?? 0);
    }

    public function add(Product $product, int $quantity = 1): int
    {
        return $this->set($product, $this->quantityOf($product->bmuk_no) + $quantity);
    }

    /**
     * Set a line's quantity, clamped to what is on the shelf. Returns the quantity kept.
     */
    public function set(Product $product, int $quantity): int
    {
        $quantity = max(0, min($quantity, $product->stock, config('store.cart_max_quantity')));

        if ($quantity === 0) {
            $this->remove($product->bmuk_no);

            return 0;
        }

        if ($user = $this->user()) {
            Cart::updateOrCreate(
                ['user_id' => $user->id, 'bmuk_no' => $product->bmuk_no],
                ['quantity' => $quantity],
            );
        } else {
            $items = $this->session->get(self::SESSION_ITEMS, []);
            $items[$product->bmuk_no] = $quantity;
            $this->session->put(self::SESSION_ITEMS, $items);
        }

        $this->lines = null;

        return $quantity;
    }

    public function remove(int $bmukNo): void
    {
        if ($user = $this->user()) {
            Cart::where('user_id', $user->id)->where('bmuk_no', $bmukNo)->delete();
        } else {
            $this->session->forget(self::SESSION_ITEMS.'.'.$bmukNo);
        }

        $this->lines = null;
    }

    public function clear(): void
    {
        if ($user = $this->user()) {
            Cart::where('user_id', $user->id)->delete();
        }

        $this->session->forget([self::SESSION_ITEMS, self::SESSION_COUPON]);
        $this->lines = null;
    }

    /**
     * Move the guest cart into the signed-in customer's cart.
     */
    public function mergeGuestCartInto(User $user): void
    {
        $guestItems = $this->session->pull(self::SESSION_ITEMS, []);

        if ($guestItems === []) {
            return;
        }

        $existing = Cart::where('user_id', $user->id)->pluck('quantity', 'bmuk_no');
        $products = Product::whereIn('bmuk_no', array_keys($guestItems))->get()->keyBy('bmuk_no');

        foreach ($guestItems as $bmukNo => $quantity) {
            if (! $product = $products->get($bmukNo)) {
                continue;
            }

            $total = min((int) $quantity + (int) ($existing[$bmukNo] ?? 0), $product->stock, config('store.cart_max_quantity'));

            if ($total > 0) {
                Cart::updateOrCreate(['user_id' => $user->id, 'bmuk_no' => $bmukNo], ['quantity' => $total]);
            }
        }

        $this->lines = null;
    }

    public function subtotal(): float
    {
        return round($this->lines()->sum(fn (CartLine $line) => $line->total()), 2);
    }

    /**
     * Apply a coupon code. Returns an error message, or null on success.
     */
    public function applyCoupon(string $code): ?string
    {
        $coupon = Coupon::findByCode($code);

        if (! $coupon) {
            return 'That code does not exist.';
        }

        if ($reason = $coupon->rejectionReason($this->subtotal())) {
            return $reason;
        }

        $this->session->put(self::SESSION_COUPON, $coupon->code);

        return null;
    }

    public function removeCoupon(): void
    {
        $this->session->forget(self::SESSION_COUPON);
    }

    /**
     * The applied coupon, dropped automatically once it stops being valid for this cart.
     */
    public function coupon(): ?Coupon
    {
        $coupon = Coupon::findByCode($this->session->get(self::SESSION_COUPON));

        if ($coupon && $coupon->rejectionReason($this->subtotal()) !== null) {
            return null;
        }

        return $coupon;
    }

    public function discount(): float
    {
        return $this->coupon()?->discountFor($this->subtotal()) ?? 0.0;
    }

    public function shippingCost(?ShippingMethod $method): float
    {
        if (! $method || $this->isEmpty()) {
            return 0.0;
        }

        return $this->qualifiesForFreeShipping() ? 0.0 : $method->cost();
    }

    public function qualifiesForFreeShipping(): bool
    {
        return $this->subtotal() - $this->discount() >= (float) config('store.free_shipping_threshold');
    }

    public function amountToFreeShipping(): float
    {
        return max(0, (float) config('store.free_shipping_threshold') - ($this->subtotal() - $this->discount()));
    }

    public function total(?ShippingMethod $method = null): float
    {
        return round($this->subtotal() - $this->discount() + $this->shippingCost($method), 2);
    }

    /**
     * @return array<int, int> bmuk_no => quantity
     */
    private function quantities(): array
    {
        if ($user = $this->user()) {
            return Cart::where('user_id', $user->id)
                ->pluck('quantity', 'bmuk_no')
                ->map(fn ($quantity) => (int) $quantity)
                ->all();
        }

        return $this->session->get(self::SESSION_ITEMS, []);
    }

    private function user(): ?User
    {
        return $this->auth->guard()->user();
    }
}
