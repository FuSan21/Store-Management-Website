<?php

use App\Livewire\CartPage;
use App\Models\Cart;
use App\Models\Coupon;
use App\Support\Cart\CartService;
use Livewire\Livewire;

test('guests can add to cart without an account', function () {
    $product = product();

    $this->post(route('cart.add'), ['bmuk_no' => $product->bmuk_no, 'quantity' => 2])->assertSessionHas('toast');

    expect(app(CartService::class)->quantityOf($product->bmuk_no))->toBe(2);
});

test('the cart never holds more than is in stock', function () {
    $product = product(['stock' => 3]);

    $this->post(route('cart.add'), ['bmuk_no' => $product->bmuk_no, 'quantity' => 10]);

    expect(app(CartService::class)->quantityOf($product->bmuk_no))->toBe(3);
});

test('out of stock products cannot be added', function () {
    $product = product(['stock' => 0]);

    $this->post(route('cart.add'), ['bmuk_no' => $product->bmuk_no]);

    expect(app(CartService::class)->isEmpty())->toBeTrue();
});

test('signed-in carts are stored in the database', function () {
    $user = customer();
    $product = product();

    $this->actingAs($user)->post(route('cart.add'), ['bmuk_no' => $product->bmuk_no]);

    expect(Cart::where('user_id', $user->id)->sole()->quantity)->toBe(1);
});

test('the cart page changes quantities and removes lines', function () {
    $user = customer();
    $product = product(['stock' => 2]);
    Cart::create(['user_id' => $user->id, 'bmuk_no' => $product->bmuk_no, 'quantity' => 1]);

    $this->actingAs($user);

    Livewire::test(CartPage::class)
        ->call('increment', $product->bmuk_no)
        ->assertDispatched('cart-updated', count: 2)
        ->call('increment', $product->bmuk_no)
        ->assertDispatched('toast')
        ->call('remove', $product->bmuk_no)
        ->assertSee('Your cart is empty');

    expect(Cart::count())->toBe(0);
});

test('coupons apply, respect their minimum and come off the total', function () {
    $user = customer();
    $product = product(['price' => 1000]);
    Coupon::factory()->create(['code' => 'TENOFF', 'value' => 10, 'min_subtotal' => 1500]);
    Cart::create(['user_id' => $user->id, 'bmuk_no' => $product->bmuk_no, 'quantity' => 1]);

    $this->actingAs($user);

    Livewire::test(CartPage::class)
        ->set('couponCode', 'tenoff')
        ->call('applyCoupon')
        ->assertHasErrors('couponCode')
        ->call('increment', $product->bmuk_no)
        ->set('couponCode', 'tenoff')
        ->call('applyCoupon')
        ->assertHasNoErrors();

    $cart = app(CartService::class);
    expect($cart->discount())->toBe(200.0)
        ->and($cart->total())->toBe(1800.0);
});

test('expired and used-up coupons are refused', function () {
    $product = product(['price' => 1000]);
    Coupon::factory()->create(['code' => 'OLD', 'expires_at' => now()->subDay()]);
    Coupon::factory()->create(['code' => 'GONE', 'max_uses' => 1, 'used_count' => 1]);
    $this->post(route('cart.add'), ['bmuk_no' => $product->bmuk_no]);

    $cart = app(CartService::class);

    expect($cart->applyCoupon('OLD'))->toBe('This code has expired.')
        ->and($cart->applyCoupon('GONE'))->toBe('This code has been fully redeemed.')
        ->and($cart->applyCoupon('NOPE'))->toBe('That code does not exist.');
});
