<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('storefront.cart');
    }

    /**
     * Add to cart from any product card or page; works without JavaScript.
     */
    public function store(Request $request, CartService $cart): RedirectResponse
    {
        $data = $request->validate([
            'bmuk_no' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.config('store.cart_max_quantity')],
        ]);

        $product = Product::findOrFail($data['bmuk_no']);

        if (! $product->in_stock) {
            return back()->with('toast', ['type' => 'error', 'message' => "{$product->title} is out of stock."]);
        }

        $before = $cart->quantityOf($product->bmuk_no);
        $after = $cart->add($product, $data['quantity'] ?? 1);

        $toast = $after > $before
            ? ['type' => 'success', 'message' => 'Added to cart', 'action' => ['label' => 'View cart', 'url' => route('cart')]]
            : ['type' => 'error', 'message' => "You already have all {$product->stock} in stock in your cart."];

        return $request->boolean('buy_now') ? redirect()->route('checkout') : back()->with('toast', $toast);
    }
}
