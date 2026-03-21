<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Popular models on Bangladeshi roads, by brand.
     */
    public const VEHICLES = [
        'Toyota' => ['Corolla', 'Allion', 'Premio', 'Axio', 'Noah', 'Hiace', 'Prado'],
        'Honda' => ['Civic', 'Vezel', 'Grace', 'City', 'CR-V'],
        'Nissan' => ['X-Trail', 'Sunny', 'Bluebird'],
        'Mitsubishi' => ['Pajero', 'Lancer', 'Outlander'],
        'Suzuki' => ['Swift', 'Wagon R', 'Ciaz'],
        'Hyundai' => ['Tucson', 'Elantra', 'Creta'],
    ];

    /**
     * Filter collections and the media each comes in.
     */
    public const COLLECTIONS = [
        'Oil Filter' => ['Spin-on', 'Cartridge'],
        'Air Filter' => ['Panel', 'Polyurethane'],
        'Fuel Filter' => ['Inline', 'Cartridge'],
        'Cabin Filter' => ['Activated Carbon', 'Particulate'],
    ];

    public function definition(): array
    {
        $brand = fake()->randomElement(array_keys(self::VEHICLES));
        $collection = fake()->randomElement(array_keys(self::COLLECTIONS));

        return [
            'euro_id' => 'EU-'.fake()->unique()->numerify('######'),
            'oem_id' => fake()->unique()->bothify('#####-??###'),
            'image' => '',
            'description' => null,
            'price' => fake()->randomElement([450, 650, 850, 1150, 1450, 1850, 2400, 3200]),
            'stock' => fake()->numberBetween(0, 60),
            'low_stock_threshold' => 5,
            'is_featured' => false,
            'collection' => $collection,
            'filter_type' => fake()->randomElement(self::COLLECTIONS[$collection]),
            'car_brand' => $brand,
            'model' => fake()->randomElement(self::VEHICLES[$brand]),
            'specification' => $this->fitments(),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }

    /**
     * @return array<int, array{engine: string, fuelType: string, year: string}>
     */
    private function fitments(): array
    {
        return collect(range(1, fake()->numberBetween(1, 4)))
            ->map(fn () => [
                'engine' => fake()->randomElement(['1.3L', '1.5L', '1.8L', '2.0L', '2.4L', '2.7L']),
                'fuelType' => fake()->randomElement(['Petrol', 'Diesel', 'Hybrid', 'CNG']),
                'year' => fake()->randomElement(['2008-2013', '2012-2018', '2014-2020', '2016-2022', '2018-2024', '2020-2025']),
            ])
            ->unique(fn ($row) => implode('|', $row))
            ->values()
            ->all();
    }
}
