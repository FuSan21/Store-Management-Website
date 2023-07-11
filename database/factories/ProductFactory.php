<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'euro_id' => $this->faker->unique->uuid,
            'oem_id' => $this->faker->uuid,
            'collection' => $this->faker->randomElement(array('Air Filter', 'Oil Filter', '0w-30')),
            'filter_type' => $this->faker->randomElement(array('Polyurethane', 'Plastic Panel', 'Metal Cap')),
            'car_brand' => $this->faker->randomElement(array('Audi', 'BMW', 'Castrol', 'Citroen', 'Fiat', 'Ford')),
            'model' => $this->faker->randomElement(array('B-Max', 'C-Max', 'EcoSport'))
        ];
    }
}
