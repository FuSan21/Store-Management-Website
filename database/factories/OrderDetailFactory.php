<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'bmuk_no' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 3),
            'unit_price' => fn (array $attributes) => Product::find($attributes['bmuk_no'])?->price ?? 1000,
        ];
    }
}
