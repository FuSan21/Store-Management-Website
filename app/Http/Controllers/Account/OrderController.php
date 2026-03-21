<?php

namespace App\Http\Controllers\Account;

use App\Actions\ChangeOrderStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->enum('status', OrderStatus::class);

        $orders = $request->user()->orders()
            ->with('orderDetails')
            ->when($status, fn ($query) => $query->where('status', $status->value))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('account.orders.index', ['orders' => $orders, 'status' => $status]);
    }

    public function show(Request $request, Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load(['orderDetails.product', 'statusHistories']);

        $reviewed = $request->user()->reviews()
            ->where('order_id', $order->id)
            ->pluck('product_id')
            ->all();

        return view('account.orders.show', ['order' => $order, 'reviewed' => $reviewed]);
    }

    public function invoice(Order $order): Response
    {
        Gate::authorize('view', $order);

        $order->load(['orderDetails.product', 'user']);

        return Pdf::loadView('pdf.invoice', ['order' => $order])
            ->setPaper('a4')
            ->download('invoice-'.ltrim($order->number(), '#').'.pdf');
    }

    public function cancel(Request $request, Order $order, ChangeOrderStatus $changeStatus): RedirectResponse
    {
        Gate::authorize('cancel', $order);

        $changeStatus->handle($order, OrderStatus::Cancelled, $request->user(), 'Cancelled by the customer');

        return back()->with('toast', ['type' => 'success', 'message' => "Order {$order->number()} cancelled."]);
    }
}
