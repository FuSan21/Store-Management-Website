<?php

namespace App\Http\Controllers\Account;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $orders = $user->orders();

        return view('account.dashboard', [
            'recentOrders' => $user->orders()->with('orderDetails')->latest()->limit(5)->get(),
            'stats' => [
                'orders' => (clone $orders)->count(),
                'open' => (clone $orders)->whereIn('status', [
                    OrderStatus::WaitingForPayment->value, OrderStatus::Processing->value, OrderStatus::Shipped->value,
                ])->count(),
                'spent' => (float) (clone $orders)->countingTowardsRevenue()->sum('total_cost'),
                'wishlist' => $user->wishlistItems()->count(),
            ],
            'toReview' => $this->itemsAwaitingReview($request),
        ]);
    }

    /**
     * Delivered items the customer has not reviewed yet.
     */
    private function itemsAwaitingReview(Request $request)
    {
        $user = $request->user();

        return $user->orders()
            ->where('status', OrderStatus::Completed->value)
            ->with('orderDetails.product')
            ->latest()
            ->limit(10)
            ->get()
            ->flatMap(fn ($order) => $order->orderDetails->map(fn ($line) => ['order' => $order, 'product' => $line->product]))
            ->filter(fn ($item) => $item['product'] && ! $user->reviews()
                ->where('order_id', $item['order']->id)
                ->where('product_id', $item['product']->bmuk_no)
                ->exists())
            ->unique(fn ($item) => $item['product']->bmuk_no)
            ->take(3)
            ->values();
    }
}
