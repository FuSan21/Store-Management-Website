<?php

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

function orderFor(User $user, OrderStatus $status = OrderStatus::WaitingForPayment, ?Product $product = null): Order
{
    $order = Order::factory()->for($user)->status($status)->create();
    $order->orderDetails()->create(['bmuk_no' => ($product ?? product())->bmuk_no, 'quantity' => 2, 'unit_price' => 1000]);
    $order->statusHistories()->create(['status' => $status]);

    return $order;
}

test('account pages render', function (string $route) {
    $user = customer();
    orderFor($user);

    $this->actingAs($user)->get(route($route))->assertOk();
})->with(['account.dashboard', 'account.orders.index', 'account.wishlist', 'account.addresses.index', 'account.profile.edit']);

test('customers see their own order with its timeline', function () {
    $user = customer();
    $order = orderFor($user, OrderStatus::Shipped);

    $this->actingAs($user)->get(route('account.orders.show', $order))
        ->assertOk()
        ->assertSee($order->number())
        ->assertSee('Shipped');
});

test("customers cannot open another customer's order or invoice", function () {
    $order = orderFor(customer());

    $this->actingAs(customer())->get(route('account.orders.show', $order))->assertForbidden();
    $this->get(route('account.orders.invoice', $order))->assertForbidden();
});

test('the invoice downloads as a pdf', function () {
    $user = customer();
    $order = orderFor($user);

    $this->actingAs($user)->get(route('account.orders.invoice', $order))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('customers cancel unpaid orders and the stock goes back', function () {
    $user = customer();
    $product = product(['stock' => 3]);
    $order = orderFor($user, product: $product);

    $this->actingAs($user)->post(route('account.orders.cancel', $order))->assertSessionHas('toast');

    expect($order->fresh()->status)->toBe(OrderStatus::Cancelled)
        ->and($product->fresh()->stock)->toBe(5);
});

test('orders already being processed cannot be cancelled by the customer', function () {
    $user = customer();
    $order = orderFor($user, OrderStatus::Processing);

    $this->actingAs($user)->post(route('account.orders.cancel', $order))->assertForbidden();
});

test('customers review delivered items once', function () {
    $user = customer();
    $product = product();
    $order = orderFor($user, OrderStatus::Completed, $product);

    $this->actingAs($user)->get(route('account.reviews.create', [$order, $product]))->assertOk();
    $this->post(route('account.reviews.store', [$order, $product]), ['rating' => 5, 'comment' => 'Perfect fit on my Axio.'])
        ->assertRedirect(route('account.orders.show', $order));
    $this->post(route('account.reviews.store', [$order, $product]), ['rating' => 1, 'comment' => 'Trying to review twice.'])
        ->assertStatus(409);

    expect(Review::count())->toBe(1);
});

test('items from undelivered orders cannot be reviewed', function () {
    $user = customer();
    $product = product();
    $order = orderFor($user, OrderStatus::Processing, $product);

    $this->actingAs($user)->post(route('account.reviews.store', [$order, $product]), ['rating' => 5, 'comment' => 'Not delivered yet though.'])
        ->assertForbidden();
});

test('the wishlist toggles on and off', function () {
    $user = customer();
    $product = product();

    $this->actingAs($user)->post(route('wishlist.toggle', $product));
    expect(Wishlist::count())->toBe(1);

    $this->post(route('wishlist.toggle', $product));
    expect(Wishlist::count())->toBe(0);
});

test('the address book keeps exactly one default', function () {
    $user = customer();
    $first = $user->addresses->first();

    $this->actingAs($user)->post(route('account.addresses.store'), [
        'label' => 'Office', 'recipient' => 'Rahim', 'line' => 'Gulshan Avenue', 'city' => 'Dhaka', 'is_default_shipping' => 1,
    ])->assertRedirect(route('account.addresses.index'));

    expect($first->fresh()->is_default_shipping)->toBeFalse()
        ->and(Address::where('user_id', $user->id)->where('is_default_shipping', true)->count())->toBe(1);
});

test("customers cannot edit someone else's address", function () {
    $address = customer()->addresses->first();

    $this->actingAs(customer())->get(route('account.addresses.edit', $address))->assertForbidden();
    $this->delete(route('account.addresses.destroy', $address))->assertForbidden();
});

test('customers change their password and delete their account', function () {
    $user = customer();

    $this->actingAs($user)->put(route('account.profile.password'), [
        'current_password' => 'password', 'password' => 'new-password-1', 'password_confirmation' => 'new-password-1',
    ])->assertSessionHasNoErrors();

    $this->delete(route('account.profile.destroy'), ['password' => 'new-password-1'])->assertRedirect(route('home'));

    expect(User::find($user->id))->toBeNull();
});
