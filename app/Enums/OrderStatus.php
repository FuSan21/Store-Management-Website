<?php

namespace App\Enums;

enum OrderStatus: string
{
    case WaitingForPayment = 'Waiting for Payment';
    case Processing = 'Processing';
    case Shipped = 'Shipped';
    case Completed = 'Completed';
    case Suspended = 'Suspended';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return $this->value;
    }

    /**
     * Tone used by the status badge component.
     */
    public function tone(): string
    {
        return match ($this) {
            self::WaitingForPayment => 'amber',
            self::Processing => 'blue',
            self::Shipped => 'sky',
            self::Completed => 'green',
            self::Suspended => 'neutral',
            self::Cancelled => 'red',
        };
    }

    /**
     * Statuses an admin may move an order to from this one.
     *
     * @return array<int, self>
     */
    public function transitions(): array
    {
        return match ($this) {
            self::WaitingForPayment => [self::Processing, self::Suspended, self::Cancelled],
            self::Processing => [self::Shipped, self::Suspended, self::Cancelled],
            self::Shipped => [self::Completed, self::Cancelled],
            self::Suspended => [self::WaitingForPayment, self::Processing, self::Cancelled],
            self::Completed, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->transitions(), true);
    }

    /**
     * The happy path shown on the customer's order timeline.
     *
     * @return array<int, self>
     */
    public static function timeline(): array
    {
        return [self::WaitingForPayment, self::Processing, self::Shipped, self::Completed];
    }

    /**
     * Statuses that count towards revenue.
     *
     * @return array<int, self>
     */
    public static function revenue(): array
    {
        return [self::Processing, self::Shipped, self::Completed];
    }
}
