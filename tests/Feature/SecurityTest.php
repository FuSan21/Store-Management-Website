<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

test('the old state-changing GET urls no longer exist', function (string $url) {
    $this->actingAs(admin())->get($url)->assertNotFound();
})->with([
    '/customer/cart/addtocart?bmukNo=1',
    '/customer/cart/removefromcart?bmuk_no=1',
    '/customer/checkout',
    '/customer/submitreview',
    '/admin/updateOrderStatus?orderid=1&setStatus=Completed',
    '/admin/adminproductlist/deleteproduct?bmuk_no=1',
    '/admin/admincustomerlist/verifycustomer?userid=1',
]);

test('state changes require a csrf token outside tests', function () {
    $middleware = app(Kernel::class)->getMiddlewareGroups()['web'];

    expect($middleware)->toContain(PreventRequestForgery::class);
});

test('is_admin cannot be mass assigned', function () {
    $user = User::factory()->create();
    $user->fill(['is_admin' => true])->save();

    expect($user->fresh()->isAdmin())->toBeFalse();
});

test('order ids cannot be enumerated by customers', function () {
    $order = Order::factory()->create();

    $this->actingAs(customer())->get(route('account.orders.show', $order))->assertForbidden();
});
