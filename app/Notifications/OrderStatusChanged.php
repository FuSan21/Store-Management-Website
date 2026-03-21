<?php

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Order $order,
        public readonly ?string $note = null,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->order->status;

        $headline = match ($status) {
            OrderStatus::Processing => 'Payment confirmed — we are packing your order.',
            OrderStatus::Shipped => "Your order is on its way with {$this->order->shipping_method->label()}.",
            OrderStatus::Completed => 'Your order has been delivered.',
            OrderStatus::Cancelled => 'Your order has been cancelled.',
            OrderStatus::Suspended => 'Your order is on hold.',
            OrderStatus::WaitingForPayment => 'Your order is waiting for payment.',
        };

        $message = (new MailMessage)
            ->subject("Order {$this->order->number()}: {$status->label()}")
            ->greeting("Hello {$notifiable->name},")
            ->line($headline);

        if ($this->note) {
            $message->line("Note from the store: {$this->note}");
        }

        if ($status === OrderStatus::Completed) {
            $message->line('Tell other drivers how the parts fit — you can review each item from your order page.');
        }

        return $message->action('View order', route('account.orders.show', $this->order));
    }
}
