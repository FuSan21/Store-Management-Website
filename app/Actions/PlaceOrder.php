<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\ShippingMethod;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Notifications\OrderPlaced;
use App\Support\Cart\CartLine;
use App\Support\Cart\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlaceOrder
{
    public function __construct(private readonly CartService $cart) {}

    /**
     * Turn the customer's cart into an order. Stock is checked and taken under
     * row locks so two customers cannot buy the last unit at the same time.
     *
     * @throws ValidationException when the cart is empty or stock ran out
     */
    public function handle(
        User $user,
        ShippingMethod $shippingMethod,
        PaymentMethod $paymentMethod,
        string $shippingAddress,
        string $billingAddress,
        ?string $phone = null,
        ?string $notes = null,
    ): Order {
        $lines = $this->cart->lines();

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        $order = DB::transaction(function () use ($user, $lines, $shippingMethod, $paymentMethod, $shippingAddress, $billingAddress, $phone, $notes) {
            $products = Product::whereIn('bmuk_no', $lines->map(fn (CartLine $line) => $line->product->bmuk_no))
                ->lockForUpdate()
                ->get()
                ->keyBy('bmuk_no');

            $shortages = $lines
                ->filter(fn (CartLine $line) => $line->quantity > ($products[$line->product->bmuk_no]->stock ?? 0))
                ->map(fn (CartLine $line) => $line->product->title.' (only '.($products[$line->product->bmuk_no]->stock ?? 0).' left)');

            if ($shortages->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Some items no longer have enough stock: '.$shortages->implode('; ').'.',
                ]);
            }

            $subtotal = $this->cart->subtotal();
            $coupon = $this->cart->coupon();
            $discount = $coupon?->discountFor($subtotal) ?? 0.0;
            $shippingCost = $this->cart->shippingCost($shippingMethod);

            $order = Order::create([
                'user_id' => $user->id,
                'status' => OrderStatus::WaitingForPayment,
                'payment_method' => $paymentMethod,
                'shipping_method' => $shippingMethod,
                'shipping_address' => mb_substr($shippingAddress, 0, 255),
                'billing_address' => mb_substr($billingAddress, 0, 255),
                'phone' => $phone,
                'notes' => $notes,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'coupon_code' => $coupon?->code,
                'total_cost' => round($subtotal - $discount + $shippingCost, 2),
            ]);

            foreach ($lines as $line) {
                $product = $products[$line->product->bmuk_no];

                $order->orderDetails()->create([
                    'bmuk_no' => $product->bmuk_no,
                    'quantity' => $line->quantity,
                    'unit_price' => $product->price,
                ]);

                $product->decrement('stock', $line->quantity);

                StockMovement::create([
                    'bmuk_no' => $product->bmuk_no,
                    'change' => -$line->quantity,
                    'stock_after' => $product->stock,
                    'reason' => StockMovement::REASON_SALE,
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                ]);
            }

            if ($coupon) {
                Coupon::whereKey($coupon->id)->increment('used_count');
            }

            $order->statusHistories()->create([
                'status' => OrderStatus::WaitingForPayment,
                'note' => 'Order placed',
                'changed_by' => $user->id,
            ]);

            return $order;
        });

        $this->cart->clear();

        $user->notify(new OrderPlaced($order));

        return $order;
    }
}
