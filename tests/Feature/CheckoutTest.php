<?php

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\StockMovement;
use App\Models\User;
use App\Notifications\OrderPlaced;
use App\Support\Cart\CartService;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

test('guests are sent to sign in before checkout', function () {
    $this->get(route('checkout'))->assertRedirect(route('login'));
});

test('an empty cart cannot check out', function () {
    $this->actingAs(customer())->get(route('checkout'))->assertRedirect(route('cart'));
});

test('placing an order snapshots prices, takes stock and empties the cart', function () {
    $user = customer();
    $product = product(['price' => 1200, 'stock' => 5]);
    Cart::create(['user_id' => $user->id, 'bmuk_no' => $product->bmuk_no, 'quantity' => 2]);

    $this->actingAs($user)->get(route('checkout'))->assertOk()->assertSee('Place order');

    $response = $this->post(route('checkout.store'), checkoutPayload($user));

    $order = Order::sole();
    $response->assertRedirect(route('account.orders.show', $order));

    expect($order->status)->toBe(OrderStatus::WaitingForPayment)
        ->and((float) $order->subtotal)->toBe(2400.0)
        ->and((float) $order->shipping_cost)->toBe(80.0)
        ->and((float) $order->total_cost)->toBe(2480.0)
        ->and($order->shipping_address)->toContain($user->addresses->first()->line)
        ->and((float) $order->orderDetails->sole()->unit_price)->toBe(1200.0)
        ->and($product->fresh()->stock)->toBe(3)
        ->and(StockMovement::sole()->change)->toBe(-2)
        ->and($order->statusHistories)->toHaveCount(1)
        ->and(Cart::count())->toBe(0);

    Notification::assertSentTo($user, OrderPlaced::class);
});

test('a later price change does not change past orders', function () {
    $user = customer();
    $product = product(['price' => 1000]);
    Cart::create(['user_id' => $user->id, 'bmuk_no' => $product->bmuk_no, 'quantity' => 1]);
    $this->actingAs($user)->post(route('checkout.store'), checkoutPayload($user));

    $product->update(['price' => 9999]);

    expect(Order::sole()->orderDetails->sole()->price())->toBe(1000.0);
});

test('orders over the threshold ship free and coupons are redeemed', function () {
    config(['store.free_shipping_threshold' => 5000]);
    $user = customer();
    $product = product(['price' => 3000]);
    $coupon = Coupon::factory()->fixed(500)->create(['code' => 'SAVE500']);
    Cart::create(['user_id' => $user->id, 'bmuk_no' => $product->bmuk_no, 'quantity' => 2]);

    $this->actingAs($user);
    app(CartService::class)->applyCoupon('SAVE500');
    $this->post(route('checkout.store'), checkoutPayload($user));

    $order = Order::sole();
    expect((float) $order->discount)->toBe(500.0)
        ->and((float) $order->shipping_cost)->toBe(0.0)
        ->and((float) $order->total_cost)->toBe(5500.0)
        ->and($order->coupon_code)->toBe('SAVE500')
        ->and($coupon->fresh()->used_count)->toBe(1);
});

test('checkout stops when stock ran out after the item was added', function () {
    $user = customer();
    $product = product(['stock' => 5]);
    Cart::create(['user_id' => $user->id, 'bmuk_no' => $product->bmuk_no, 'quantity' => 4]);
    $product->update(['stock' => 1]);

    $this->actingAs($user)->post(route('checkout.store'), checkoutPayload($user))->assertSessionHasErrors('cart');

    expect(Order::count())->toBe(0)->and($product->fresh()->stock)->toBe(1);
});

test('checkout validates the phone, courier and address', function () {
    $user = customer();
    $other = customer();
    Cart::create(['user_id' => $user->id, 'bmuk_no' => product()->bmuk_no, 'quantity' => 1]);

    $this->actingAs($user)->post(route('checkout.store'), checkoutPayload($user, [
        'phone' => '12345',
        'shipping_method' => 'Pigeon',
        'shipping_address_id' => $other->addresses->first()->id,
    ]))->assertSessionHasErrors(['phone', 'shipping_method', 'shipping_address_id']);
});

test('a new address typed at checkout can be saved to the address book', function () {
    $user = User::factory()->create();
    Cart::create(['user_id' => $user->id, 'bmuk_no' => product()->bmuk_no, 'quantity' => 1]);

    $this->actingAs($user)->post(route('checkout.store'), [
        'new_address' => ['recipient' => 'Nadia Rahman', 'line' => 'Road 27, Dhanmondi', 'city' => 'Dhaka'],
        'save_address' => 1,
        'phone' => '01812345678',
        'shipping_method' => 'Sundarban',
        'payment_method' => 'COD',
    ])->assertSessionHasNoErrors();

    expect(Order::sole()->shipping_address)->toBe('Nadia Rahman, Road 27, Dhanmondi, Dhaka')
        ->and($user->addresses()->sole()->is_default_shipping)->toBeTrue();
});
