<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactMessageReceived extends Notification
{
    use Queueable;

    /**
     * @param  array{name: string, email: string, vehicle?: string|null, message: string}  $message
     */
    public function __construct(public readonly array $message) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Website message from '.$this->message['name'])
            ->replyTo($this->message['email'], $this->message['name'])
            ->line('From: '.$this->message['name'].' <'.$this->message['email'].'>')
            ->line('Vehicle: '.($this->message['vehicle'] ?? '—'))
            ->line($this->message['message']);
    }
}
