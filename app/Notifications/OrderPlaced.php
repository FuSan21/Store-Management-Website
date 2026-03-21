<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order->loadMissing('orderDetails.product');

        $message = (new MailMessage)
            ->subject("Order {$order->number()} received")
            ->greeting("Thanks, {$notifiable->name}.")
            ->line("We have your order {$order->number()} for ".money($order->total_cost).'.');

        foreach ($order->orderDetails as $line) {
            $message->line("{$line->quantity} × {$line->product?->title} — ".money($line->lineTotal()));
        }

        return $message
            ->line("Payment: {$order->payment_method->label()}. Delivery by {$order->shipping_method->label()} ({$order->shipping_method->eta()}).")
            ->action('Track your order', route('account.orders.show', $order));
    }
}
