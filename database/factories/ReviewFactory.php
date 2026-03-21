<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    public const COMMENTS = [
        'Exact fit, the gasket seated first time.',
        'Same part number as the one the workshop used, half the price.',
        'Arrived in two days, well packed.',
        'Fitment list was right for my engine. Will reorder at the next service.',
        'Good quality media, noticeably cleaner cabin air.',
        'Box was a little dented but the filter itself was perfect.',
    ];

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'order_id' => Order::factory(),
            'rating' => fake()->numberBetween(3, 5),
            'comment' => fake()->randomElement(self::COMMENTS),
        ];
    }
}
