<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\Admin;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/

Route::controller(StorefrontController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('shop', 'shop')->name('shop');
    Route::get('about', 'about')->name('about');
    Route::get('contact', 'contact')->name('contact');
    Route::post('contact', 'sendMessage')->middleware('throttle:contact')->name('contact.send');
});

Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('cart', [CartController::class, 'index'])->name('cart');
Route::post('cart/items', [CartController::class, 'store'])->name('cart.add');

/*
|--------------------------------------------------------------------------
| Signed-in customers
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('checkout', [CheckoutController::class, 'create'])->name('checkout');
    Route::post('checkout', [CheckoutController::class, 'store'])->middleware('throttle:checkout')->name('checkout.store');

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', Account\DashboardController::class)->name('dashboard');
        Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist');

        Route::controller(Account\OrderController::class)->group(function () {
            Route::get('orders', 'index')->name('orders.index');
            Route::get('orders/{order}', 'show')->name('orders.show');
            Route::get('orders/{order}/invoice', 'invoice')->name('orders.invoice');
            Route::post('orders/{order}/cancel', 'cancel')->name('orders.cancel');
        });

        Route::controller(Account\ReviewController::class)->group(function () {
            Route::get('orders/{order}/review/{product}', 'create')->name('reviews.create');
            Route::post('orders/{order}/review/{product}', 'store')->middleware('throttle:reviews')->name('reviews.store');
        });

        Route::resource('addresses', Account\AddressController::class)->except('show');

        Route::controller(Account\ProfileController::class)->group(function () {
            Route::get('profile', 'edit')->name('profile.edit');
            Route::patch('profile', 'update')->name('profile.update');
            Route::put('profile/password', 'updatePassword')->name('profile.password');
            Route::delete('profile', 'destroy')->name('profile.destroy');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::controller(Admin\OrderController::class)->group(function () {
        Route::get('orders', 'index')->name('orders.index');
        Route::get('orders/export', 'export')->name('orders.export');
        Route::get('orders/{order}', 'show')->name('orders.show');
        Route::patch('orders/{order}/status', 'updateStatus')->name('orders.status');
    });

    Route::get('products/export', [Admin\ProductController::class, 'export'])->name('products.export');
    Route::post('products/{product}/stock', [Admin\ProductController::class, 'adjustStock'])->name('products.stock');
    Route::resource('products', Admin\ProductController::class)->except('show');

    Route::get('inventory', Admin\InventoryController::class)->name('inventory');

    Route::controller(Admin\CustomerController::class)->group(function () {
        Route::get('customers', 'index')->name('customers.index');
        Route::get('customers/export', 'export')->name('customers.export');
        Route::get('customers/{user}', 'show')->name('customers.show');
        Route::patch('customers/{user}/verify', 'verify')->name('customers.verify');
    });

    Route::resource('coupons', Admin\CouponController::class)->except('show');
});

/*
|--------------------------------------------------------------------------
| Old URLs from the Laravel 8 site
|--------------------------------------------------------------------------
*/

Route::permanentRedirect('about-us', '/about');
Route::permanentRedirect('contact-us', '/contact');
Route::get('product/{id}', fn (int $id) => redirect()->route('products.show', $id, 301))->whereNumber('id');
Route::permanentRedirect('customer/dashboard', '/account');
Route::permanentRedirect('customer/orders', '/account/orders');
Route::permanentRedirect('customer/wishlist', '/account/wishlist');
Route::permanentRedirect('customer/cart', '/cart');
Route::permanentRedirect('customer/usersetting', '/account/profile');
Route::permanentRedirect('admin/admin-dashboard', '/admin');

require __DIR__.'/auth.php';
