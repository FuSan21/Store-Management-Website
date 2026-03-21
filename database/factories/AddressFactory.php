<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => 'Home',
            'recipient' => fake()->name(),
            'phone' => '018'.fake()->numerify('########'),
            'line' => fake()->streetName().', '.fake()->randomElement(['Dhanmondi', 'Gulshan', 'Mirpur', 'Uttara', 'Banani']),
            'city' => fake()->randomElement(['Dhaka', 'Chattogram', 'Sylhet', 'Khulna']),
            'is_default_shipping' => true,
            'is_default_billing' => true,
        ];
    }
}
