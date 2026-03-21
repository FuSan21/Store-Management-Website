<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ChangeOrderStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $this->filtered($request)
            ->with(['user:id,name,email', 'orderDetails'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = Order::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.orders.index', [
            'orders' => $orders,
            'counts' => $counts,
            'filters' => $request->only(['q', 'status', 'from', 'to']),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'orderDetails.product', 'statusHistories.changedBy:id,name']);

        return view('admin.orders.show', ['order' => $order]);
    }

    public function updateStatus(Request $request, Order $order, ChangeOrderStatus $changeStatus): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $changeStatus->handle($order, OrderStatus::from($data['status']), $request->user(), $data['note'] ?? null);

        return back()->with('toast', ['type' => 'success', 'message' => "Order {$order->number()} is now {$order->status->label()}. The customer has been emailed."]);
    }

    public function export(Request $request): StreamedResponse
    {
        $orders = $this->filtered($request)->with(['user:id,name,email', 'orderDetails'])->latest();

        return Csv::download('orders-'.now()->format('Y-m-d').'.csv', [
            'Order', 'Placed', 'Status', 'Customer', 'Email', 'Phone', 'Items', 'Subtotal', 'Discount', 'Coupon',
            'Shipping', 'Total', 'Payment', 'Courier', 'Shipping address',
        ], function () use ($orders) {
            foreach ($orders->lazy(200) as $order) {
                yield [
                    $order->number(),
                    $order->created_at->format('Y-m-d H:i'),
                    $order->status->label(),
                    $order->user?->name,
                    $order->user?->email,
                    $order->phone,
                    $order->itemCount(),
                    $order->subtotal,
                    $order->discount,
                    $order->coupon_code,
                    $order->shipping_cost,
                    $order->total_cost,
                    $order->payment_method->label(),
                    $order->shipping_method->label(),
                    $order->shipping_address,
                ];
            }
        });
    }

    private function filtered(Request $request): Builder
    {
        return Order::query()
            ->when($request->enum('status', OrderStatus::class), fn (Builder $query, OrderStatus $status) => $query->where('status', $status->value))
            ->when($request->date('from'), fn (Builder $query, $from) => $query->where('created_at', '>=', $from->startOfDay()))
            ->when($request->date('to'), fn (Builder $query, $to) => $query->where('created_at', '<=', $to->endOfDay()))
            ->when(trim((string) $request->input('q')), function (Builder $query, string $term) {
                $query->where(fn (Builder $q) => $q
                    ->where('id', (int) ltrim($term, '#0') ?: -1)
                    ->orWhereLike('phone', "%{$term}%")
                    ->orWhereHas('user', fn (Builder $user) => $user
                        ->whereLike('name', "%{$term}%")
                        ->orWhereLike('email', "%{$term}%")));
            });
    }
}
