<?php

use App\Models\Address;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

function admin(): User
{
    return User::factory()->admin()->create();
}

function customer(array $attributes = []): User
{
    $user = User::factory()->create($attributes);
    Address::factory()->for($user)->create(['recipient' => $user->name]);

    return $user->load('addresses');
}

function product(array $attributes = []): Product
{
    return Product::factory()->create(['stock' => 10, 'price' => 1000, ...$attributes]);
}

/**
 * A valid checkout payload using the customer's saved address.
 *
 * @return array<string, mixed>
 */
function checkoutPayload(User $user, array $overrides = []): array
{
    return [
        'shipping_address_id' => $user->addresses->first()->id,
        'phone' => '01712345678',
        'shipping_method' => 'Steadfast',
        'payment_method' => 'bkash',
        ...$overrides,
    ];
}
