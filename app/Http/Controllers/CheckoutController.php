<?php

namespace App\Http\Controllers;

use App\Actions\PlaceOrder;
use App\Enums\PaymentMethod;
use App\Enums\ShippingMethod;
use App\Http\Requests\CheckoutRequest;
use App\Support\Cart\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request, CartService $cart): View|RedirectResponse
    {
        if ($cart->isEmpty()) {
            return redirect()->route('cart');
        }

        return view('storefront.checkout', [
            'cart' => $cart,
            'addresses' => $request->user()->addresses()->latest()->get(),
            'shippingMethods' => ShippingMethod::cases(),
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function store(CheckoutRequest $request, PlaceOrder $placeOrder): RedirectResponse
    {
        $order = $placeOrder->handle(
            user: $request->user(),
            shippingMethod: $request->enum('shipping_method', ShippingMethod::class),
            paymentMethod: $request->enum('payment_method', PaymentMethod::class),
            shippingAddress: $request->shippingAddress(),
            billingAddress: $request->billingAddress(),
            phone: $request->validated('phone'),
            notes: $request->validated('notes'),
        );

        if ($request->boolean('save_address') && $request->filled('new_address.line')) {
            $request->user()->addresses()->create([
                'label' => 'Home',
                'recipient' => $request->input('new_address.recipient'),
                'phone' => $request->validated('phone'),
                'line' => $request->input('new_address.line'),
                'city' => $request->input('new_address.city'),
                'is_default_shipping' => $request->user()->addresses()->doesntExist(),
                'is_default_billing' => $request->user()->addresses()->doesntExist(),
            ]);
        }

        return redirect()
            ->route('account.orders.show', $order)
            ->with('toast', ['type' => 'success', 'message' => "Order {$order->number()} placed. A confirmation is on its way to your inbox."]);
    }
}
