<?php

namespace App\Enums;

enum ShippingMethod: string
{
    case Steadfast = 'Steadfast';
    case Sundarban = 'Sundarban';
    case ECourier = 'eCourier';

    public function label(): string
    {
        return config("store.shipping.{$this->value}.label", $this->value);
    }

    public function eta(): string
    {
        return config("store.shipping.{$this->value}.eta", '');
    }

    public function cost(): float
    {
        return (float) config("store.shipping.{$this->value}.cost", 0);
    }
}
