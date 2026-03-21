<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\ShippingMethod;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $shipping = fake()->randomElement(ShippingMethod::cases());
        $subtotal = fake()->randomElement([900, 1700, 2600, 3400, 5200]);
        $address = fake()->streetName().', '.fake()->randomElement(['Dhanmondi', 'Gulshan', 'Mirpur', 'Uttara', 'Agrabad', 'Zindabazar']);

        return [
            'user_id' => User::factory(),
            'status' => OrderStatus::WaitingForPayment,
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'shipping_method' => $shipping,
            'shipping_address' => $address,
            'billing_address' => $address,
            'phone' => '017'.fake()->numerify('########'),
            'subtotal' => $subtotal,
            'discount' => 0,
            'shipping_cost' => $shipping->cost(),
            'total_cost' => $subtotal + $shipping->cost(),
        ];
    }

    public function status(OrderStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
