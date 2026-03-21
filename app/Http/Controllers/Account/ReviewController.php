<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(Request $request, Order $order, Product $product): View|RedirectResponse
    {
        $this->authorizeReview($request, $order, $product);

        return view('account.reviews.create', ['order' => $order, 'product' => $product]);
    }

    public function store(Request $request, Order $order, Product $product): RedirectResponse
    {
        $this->authorizeReview($request, $order, $product);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->bmuk_no,
            'order_id' => $order->id,
            ...$data,
        ]);

        return redirect()
            ->route('account.orders.show', $order)
            ->with('toast', ['type' => 'success', 'message' => 'Thanks — your review is live on the product page.']);
    }

    /**
     * Only the buyer of a delivered order may review an item in it, once.
     */
    private function authorizeReview(Request $request, Order $order, Product $product): void
    {
        Gate::authorize('review', $order);

        abort_unless($order->orderDetails()->where('bmuk_no', $product->bmuk_no)->exists(), 404);

        abort_if(
            Review::where('user_id', $request->user()->id)
                ->where('order_id', $order->id)
                ->where('product_id', $product->bmuk_no)
                ->exists(),
            409,
            'You have already reviewed this item.',
        );
    }
}
