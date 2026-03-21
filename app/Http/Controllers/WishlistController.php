<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('reviews')
            ->whereIn('bmuk_no', $request->user()->wishlistItems()->select('bmuk_no'))
            ->paginate(12);

        return view('account.wishlist', ['products' => $products]);
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $removed = Wishlist::where('user_id', $request->user()->id)
            ->where('bmuk_no', $product->bmuk_no)
            ->delete();

        if (! $removed) {
            Wishlist::create(['user_id' => $request->user()->id, 'bmuk_no' => $product->bmuk_no]);
        }

        return back()->with('toast', [
            'type' => 'success',
            'message' => $removed ? 'Removed from your wishlist' : 'Saved to your wishlist',
        ]);
    }
}
