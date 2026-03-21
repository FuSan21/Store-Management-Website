<?php

use App\Livewire\HeaderSearch;
use App\Livewire\ShopCatalog;
use App\Livewire\VehicleFinder;
use App\Models\Product;
use App\Notifications\ContactMessageReceived;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('the public pages render', function (string $url) {
    product();

    $this->get($url)->assertOk();
})->with(['/', '/shop', '/about', '/contact', '/cart']);

test('the product page shows price, stock and fitment', function () {
    $product = product([
        'car_brand' => 'Toyota',
        'model' => 'Axio',
        'specification' => [['engine' => '1.5L', 'fuelType' => 'Petrol', 'year' => '2014-2020']],
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Toyota Axio 1.5L')
        ->assertSee('2014-2020')
        ->assertSee(money(1000));
});

test('a removed product returns 404', function () {
    $product = product();
    $product->delete();

    $this->get(route('products.show', $product))->assertNotFound();
});

test('legacy urls redirect permanently', function () {
    $product = product();

    $this->get('/product/'.$product->bmuk_no)->assertRedirect(route('products.show', $product))->assertStatus(301);
    $this->get('/about-us')->assertRedirect('/about');
    $this->get('/admin/admin-dashboard')->assertRedirect('/admin');
});

test('legacy placeholder images fall back to the rendered picture', function () {
    $product = product(['collection' => 'Air Filter', 'image' => 'http://127.0.0.1:8000/demo-product.png']);

    expect($product->image_url)->toEndWith('images/products/air-filter.webp');
});

test('search matches brand, model and part numbers', function () {
    $axio = product(['car_brand' => 'Toyota', 'model' => 'Axio', 'oem_id' => '15601-AB123']);
    product(['car_brand' => 'Honda', 'model' => 'Civic']);

    expect(Product::search('axio')->pluck('bmuk_no')->all())->toBe([$axio->bmuk_no])
        ->and(Product::search('15601')->pluck('bmuk_no')->all())->toBe([$axio->bmuk_no])
        ->and(Product::search((string) $axio->bmuk_no)->pluck('bmuk_no'))->toContain($axio->bmuk_no);
});

test('the fitment filter matches one engine row exactly', function () {
    $fits = product(['specification' => [['engine' => '1.5L', 'fuelType' => 'Petrol', 'year' => '2014-2020']]]);
    product(['specification' => [
        ['engine' => '1.5L', 'fuelType' => 'Diesel', 'year' => '2008-2013'],
        ['engine' => '2.0L', 'fuelType' => 'Petrol', 'year' => '2014-2020'],
    ]]);

    expect(Product::fitting('1.5L, Petrol, 2014-2020')->pluck('bmuk_no')->all())->toBe([$fits->bmuk_no]);
});

test('the shop filters by vehicle and keeps filters in the url', function () {
    $toyota = product(['car_brand' => 'Toyota', 'model' => 'Axio']);
    $honda = product(['car_brand' => 'Honda', 'model' => 'Civic']);

    Livewire::withQueryParams(['brand' => 'Toyota'])
        ->test(ShopCatalog::class)
        ->assertSee($toyota->filter_type.' · BMUK '.$toyota->bmuk_no)
        ->assertDontSee('BMUK '.$honda->bmuk_no.'<', false)
        ->set('brand', 'Honda')
        ->assertSet('model', '')
        ->assertSee('BMUK '.$honda->bmuk_no);
});

test('the shop can hide out of stock products and sort by price', function () {
    $cheap = product(['price' => 300, 'stock' => 0]);
    $dear = product(['price' => 3000]);

    $component = Livewire::test(ShopCatalog::class)->set('sort', 'price-asc');
    expect($component->get('products')->pluck('bmuk_no')->all())->toBe([$cheap->bmuk_no, $dear->bmuk_no]);

    $component->set('inStock', true);
    expect($component->get('products')->pluck('bmuk_no')->all())->toBe([$dear->bmuk_no]);
});

test('the vehicle finder narrows models and sends the visitor to the shop', function () {
    product(['car_brand' => 'Toyota', 'model' => 'Axio']);
    product(['car_brand' => 'Honda', 'model' => 'Civic']);

    Livewire::test(VehicleFinder::class)
        ->set('brand', 'Toyota')
        ->assertSee('Axio')
        ->assertDontSee('Civic')
        ->set('model', 'Axio')
        ->call('find')
        ->assertRedirect(route('shop', ['brand' => 'Toyota', 'model' => 'Axio']));
});

test('the header search suggests products', function () {
    $product = product(['car_brand' => 'Mitsubishi', 'model' => 'Pajero']);

    Livewire::test(HeaderSearch::class)
        ->set('q', 'paj')
        ->assertSee($product->title);
});

test('the contact form emails the store and blocks bots', function () {
    Notification::fake();

    $this->post(route('contact.send'), ['name' => 'Karim', 'email' => 'karim@example.com', 'message' => 'Which filter fits a 2016 Axio?'])
        ->assertSessionHasNoErrors();

    $this->post(route('contact.send'), ['name' => 'Bot', 'email' => 'bot@example.com', 'message' => 'Buy cheap stuff now!!', 'website' => 'spam'])
        ->assertSessionHasErrors('website');

    Notification::assertSentOnDemandTimes(ContactMessageReceived::class, 1);
});
