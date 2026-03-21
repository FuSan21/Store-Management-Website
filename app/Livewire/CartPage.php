<?php

namespace App\Livewire;

use App\Models\Product;
use App\Support\Cart\CartService;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class CartPage extends Component
{
    public string $couponCode = '';

    public function increment(CartService $cart, int $bmukNo): void
    {
        $this->setQuantity($cart, $bmukNo, $cart->quantityOf($bmukNo) + 1);
    }

    public function decrement(CartService $cart, int $bmukNo): void
    {
        $this->setQuantity($cart, $bmukNo, $cart->quantityOf($bmukNo) - 1);
    }

    public function setQuantity(CartService $cart, int $bmukNo, int $quantity): void
    {
        $product = Product::find($bmukNo);

        if (! $product) {
            $cart->remove($bmukNo);
        } else {
            $kept = $cart->set($product, $quantity);

            if ($kept < $quantity) {
                $this->dispatch('toast', type: 'error', message: "Only {$product->stock} in stock.");
            }
        }

        $this->dispatch('cart-updated', count: $cart->count());
    }

    public function remove(CartService $cart, int $bmukNo): void
    {
        $cart->remove($bmukNo);

        $this->dispatch('cart-updated', count: $cart->count());
        $this->dispatch('toast', type: 'success', message: 'Removed from cart');
    }

    public function applyCoupon(CartService $cart): void
    {
        $key = 'coupon:'.(auth()->id() ?: request()->ip());

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->addError('couponCode', 'Too many attempts. Try again in a minute.');

            return;
        }

        RateLimiter::hit($key);

        $this->validate(['couponCode' => ['required', 'string', 'max:40']]);

        if ($error = $cart->applyCoupon($this->couponCode)) {
            $this->addError('couponCode', $error);

            return;
        }

        $this->reset('couponCode');
        $this->dispatch('toast', type: 'success', message: 'Code applied');
    }

    public function removeCoupon(CartService $cart): void
    {
        $cart->removeCoupon();
    }

    public function render(CartService $cart)
    {
        return view('livewire.cart-page', ['cart' => $cart]);
    }
}
