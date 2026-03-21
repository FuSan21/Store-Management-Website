<?php

use App\Enums\OrderStatus;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

function adminOrder(OrderStatus $status = OrderStatus::WaitingForPayment, ?Product $product = null): Order
{
    $order = Order::factory()->for(customer())->status($status)->create();
    $order->orderDetails()->create(['bmuk_no' => ($product ?? product())->bmuk_no, 'quantity' => 3, 'unit_price' => 1000]);

    return $order;
}

test('admin pages are closed to customers and guests', function (string $url) {
    $this->get($url)->assertRedirect(route('login'));
    $this->actingAs(customer())->get($url)->assertForbidden();
})->with(['/admin', '/admin/orders', '/admin/products', '/admin/inventory', '/admin/customers', '/admin/coupons', '/admin/orders/export']);

test('admin pages render', function (string $url) {
    adminOrder(OrderStatus::Completed);

    $this->actingAs(admin())->get($url)->assertOk();
})->with(['/admin', '/admin?days=7', '/admin?days=90', '/admin/orders', '/admin/products', '/admin/products/create', '/admin/inventory', '/admin/customers', '/admin/coupons', '/admin/coupons/create']);

test('the dashboard counts revenue only from paid orders', function () {
    adminOrder(OrderStatus::Completed)->update(['total_cost' => 2000]);
    adminOrder(OrderStatus::Cancelled)->update(['total_cost' => 9000]);

    $this->actingAs(admin())->get('/admin')->assertViewHas('kpis', fn ($kpis) => $kpis['revenue']['value'] === 2000.0 && $kpis['orders']['value'] === 1);
});

test('moving an order along records history and emails the customer', function () {
    $order = adminOrder();

    $this->actingAs(admin())->patch(route('admin.orders.status', $order), ['status' => 'Processing', 'note' => 'bKash payment received'])
        ->assertSessionHas('toast');

    expect($order->fresh()->status)->toBe(OrderStatus::Processing)
        ->and($order->statusHistories()->latest('id')->first()->note)->toBe('bKash payment received');

    Notification::assertSentTo($order->user, OrderStatusChanged::class);
});

test('orders cannot jump to a status out of order', function () {
    $order = adminOrder();

    $this->actingAs(admin())->patch(route('admin.orders.status', $order), ['status' => 'Completed'])->assertSessionHasErrors('status');

    expect($order->fresh()->status)->toBe(OrderStatus::WaitingForPayment);
});

test('cancelling an order puts its items back on the shelf', function () {
    $product = product(['stock' => 2]);
    $order = adminOrder(OrderStatus::Processing, $product);

    $this->actingAs(admin())->patch(route('admin.orders.status', $order), ['status' => 'Cancelled']);

    expect($product->fresh()->stock)->toBe(5)
        ->and(StockMovement::sole()->reason)->toBe(StockMovement::REASON_CANCELLATION);
});

test('order search finds by number and customer', function () {
    $order = adminOrder();

    $this->actingAs(admin())->get('/admin/orders?q='.urlencode($order->number()))->assertSee($order->number());
    $this->get('/admin/orders?q='.urlencode($order->user->email))->assertSee($order->number());
});

test('admins add, edit and remove products', function () {
    $admin = admin();

    $this->actingAs($admin)->post(route('admin.products.store'), [
        'bmuk_no' => 9001, 'euro_id' => 'EU-9001', 'oem_id' => '90915-YZZE1',
        'collection' => 'Oil Filter', 'filter_type' => 'Spin-on', 'car_brand' => 'Toyota', 'model' => 'Corolla',
        'price' => 650, 'stock' => 12, 'low_stock_threshold' => 4,
        'specification' => [['engine' => '1.5L', 'fuelType' => 'Petrol', 'year' => '2014-2020'], ['engine' => '', 'fuelType' => '', 'year' => '']],
        'gallery_urls' => "https://cdn.example.com/a.jpg\n\nhttps://cdn.example.com/b.jpg",
    ])->assertRedirect(route('admin.products.index'));

    $product = Product::findOrFail(9001);
    expect($product->specification)->toHaveCount(1)
        ->and($product->gallery)->toBe(['https://cdn.example.com/a.jpg', 'https://cdn.example.com/b.jpg'])
        ->and(StockMovement::sole()->change)->toBe(12);

    $this->put(route('admin.products.update', $product), [
        ...$product->only(['bmuk_no', 'euro_id', 'oem_id', 'collection', 'filter_type', 'car_brand', 'model', 'low_stock_threshold', 'specification']),
        'price' => 700,
    ])->assertRedirect(route('admin.products.index'));
    expect((float) $product->fresh()->price)->toBe(700.0);

    $this->delete(route('admin.products.destroy', $product));
    expect(Product::find(9001))->toBeNull()->and(Product::withTrashed()->find(9001))->not->toBeNull();
});

test('product validation rejects bad fitment years and duplicate part numbers', function () {
    $existing = product();

    $this->actingAs(admin())->post(route('admin.products.store'), [
        'bmuk_no' => $existing->bmuk_no, 'euro_id' => $existing->euro_id, 'oem_id' => 'X', 'collection' => 'Oil Filter',
        'filter_type' => 'Spin-on', 'car_brand' => 'Toyota', 'model' => 'Axio', 'price' => 100, 'stock' => 1, 'low_stock_threshold' => 1,
        'specification' => [['engine' => '1.5L', 'fuelType' => 'Petrol', 'year' => 'last year']],
    ])->assertSessionHasErrors(['bmuk_no', 'euro_id', 'specification.0.year']);
});

test('stock adjustments are recorded on the ledger and cannot go negative', function () {
    $product = product(['stock' => 4]);

    $this->actingAs(admin())->post(route('admin.products.stock', $product), ['change' => 20, 'reason' => 'restock', 'note' => 'Invoice 4471']);
    expect($product->fresh()->stock)->toBe(24);

    $this->post(route('admin.products.stock', $product), ['change' => -30, 'reason' => 'adjustment'])->assertSessionHasErrors('change');
    expect($product->fresh()->stock)->toBe(24)->and(StockMovement::count())->toBe(1);
});

test('csv exports stream and neutralise spreadsheet formulas', function () {
    $user = customer(['name' => '=HYPERLINK("evil")']);
    Order::factory()->for($user)->create();

    $csv = $this->actingAs(admin())->get(route('admin.orders.export'))->assertOk()->streamedContent();

    expect($csv)->toContain('Order,Placed,Status')
        ->and($csv)->toContain("'=HYPERLINK");

    $this->get(route('admin.products.export'))->assertOk();
    $this->get(route('admin.customers.export'))->assertOk();
});

test('admins verify customers by hand', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs(admin())->patch(route('admin.customers.verify', $user));

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('admins manage coupons', function () {
    $this->actingAs(admin())->post(route('admin.coupons.store'), [
        'code' => 'eid-2026', 'type' => 'percent', 'value' => 15, 'is_active' => 1,
    ])->assertRedirect(route('admin.coupons.index'));

    $coupon = Coupon::sole();
    expect($coupon->code)->toBe('EID-2026');

    $this->post(route('admin.coupons.store'), ['code' => 'BAD', 'type' => 'percent', 'value' => 150])->assertSessionHasErrors('value');

    $this->delete(route('admin.coupons.destroy', $coupon));
    expect(Coupon::count())->toBe(0);
});
