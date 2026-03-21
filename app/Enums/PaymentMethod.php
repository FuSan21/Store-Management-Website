<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Bkash = 'bkash';
    case Nagad = 'Nagad';
    case Card = 'Card';
    case CashOnDelivery = 'COD';

    public function label(): string
    {
        return match ($this) {
            self::Bkash => 'bKash',
            self::Nagad => 'Nagad',
            self::Card => 'Debit or credit card',
            self::CashOnDelivery => 'Cash on delivery',
        };
    }
}
