<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\StockMovement;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChangeOrderStatus
{
    /**
     * Move an order to a new status, record it on the timeline, put stock back
     * when an order is cancelled and tell the customer.
     *
     * @throws ValidationException when the move is not allowed from the current status
     */
    public function handle(Order $order, OrderStatus $status, ?User $by = null, ?string $note = null): Order
    {
        if (! $order->status->canTransitionTo($status)) {
            throw ValidationException::withMessages([
                'status' => "An order that is {$order->status->label()} cannot move to {$status->label()}.",
            ]);
        }

        DB::transaction(function () use ($order, $status, $by, $note) {
            $order->update(['status' => $status]);

            $order->statusHistories()->create([
                'status' => $status,
                'note' => $note,
                'changed_by' => $by?->id,
            ]);

            if ($status === OrderStatus::Cancelled) {
                $this->restock($order, $by);
            }
        });

        $order->user?->notify(new OrderStatusChanged($order, $note));

        return $order;
    }

    private function restock(Order $order, ?User $by): void
    {
        $order->loadMissing('orderDetails.product');

        foreach ($order->orderDetails as $line) {
            if (! $product = $line->product) {
                continue;
            }

            $product->increment('stock', $line->quantity);

            StockMovement::create([
                'bmuk_no' => $product->bmuk_no,
                'change' => $line->quantity,
                'stock_after' => $product->stock,
                'reason' => StockMovement::REASON_CANCELLATION,
                'order_id' => $order->id,
                'user_id' => $by?->id,
            ]);
        }
    }
}
