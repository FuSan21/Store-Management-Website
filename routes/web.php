<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Models\Order;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::controller(ProductController::class)->group(
    function () {
        Route::get('/', 'index')->name('home');
        Route::get('shop', 'shop')->name('shop');
        Route::get('shop/filtered', 'filteredShop')->name('filteredShop');
        Route::get('updateModels/{brand}', 'getModels')->name('updateModels');
        Route::get('updateSpecifications/{brand}/{specs}', 'getSpecifications')->name('updateSpecifications');
        Route::get('product/{query}', 'product')->name('product');
    }
);

Route::prefix('customer')->middleware('auth')->group(
    function () {
        Route::controller(DashboardController::class)->group(
            function () {
                Route::get('dashboard', 'index')->name('dashboard');
                Route::get('wishlist', 'wishlist')->name('wishlist');
                Route::get('cart', 'cart')->name('cart');
                Route::get('checkout', 'checkout')->name('checkout');
                Route::get('usersetting', 'userSettings')->name('usersetting');
                Route::get('updateShippingAddress', 'updateShippingAddress')->name('updateShippingAddress');
                Route::get('updateBillingAddress', 'updateBillingAddress')->name('updateBillingAddress');
                Route::get('wishlist/addtowishlist', 'switchWishlist')->name('wishlist.switch');
                Route::get('cart/addtocart', 'addToCart')->name('cart.add');
                Route::get('cart/removefromcart', 'removeFromCart')->name('cart.remove');
                Route::get('cart/updatecart', 'updateCart')->name('cart.update');
            }
        );
        Route::controller(OrderController::class)->group(
            function () {
                Route::get('orders', 'index')->name('orders');
                Route::get('orderdetails/{orderNo}', 'orderDetails')->name('orderdetails');
            }
        );
    }
);

require __DIR__ . '/auth.php';
