<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
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

Route::get('/', [ProductController::class, 'index']);
Route::get('/dashboard', function () {
    return DashboardController::index();
})->middleware(['auth'])->name('dashboard');

Route::get('/usersetting', function () {
    return DashboardController::userSettings();
})->middleware(['auth'])->name('usersetting');

Route::get('/updateShippingAddress', function (Request $request) {
    return DashboardController::updateShippingAddress($request);
})->middleware(['auth']);

Route::get('/updateBillingAddress', function (Request $request) {
    return DashboardController::updateBillingAddress($request);
})->middleware(['auth']);

Route::get('/wishlist', function () {
    return DashboardController::wishlist();
})->middleware(['auth'])->name('wishlist');

Route::get('/wishlist/addtowishlist', function (Request $request) {
    return DashboardController::switchWishlist($request);
})->middleware(['auth'])->name('wishlist.switch');

Route::get('/cart', function () {
    return DashboardController::cart();
})->middleware(['auth'])->name('cart');

Route::get('/cart/addtocart', function (Request $request) {
    return DashboardController::addToCart($request);
})->middleware(['auth'])->name('cart.add');

Route::get('/cart/removefromcart', function (Request $request) {
    return DashboardController::removeFromCart($request);
})->middleware(['auth'])->name('cart.remove');

Route::get('/cart/updatecart', function (Request $request) {
    return DashboardController::updateCart($request);
})->middleware(['auth'])->name('cart.update');

Route::get('/checkout', function (Request $request) {
    return DashboardController::checkout($request);
})->middleware(['auth'])->name('checkout');

Route::get('/orderdetails/{orderNo}', function ($orderNo) {
    return OrderController::orderDetails($orderNo);
})->middleware(['auth'])->name('orderdetails');


Route::get('/orders', function () {
    return OrderController::index();
})->middleware(['auth'])->name('orders');

Route::get('/shop', function (Request $request) {
    return ProductController::shop($request);
})->name('shop');

Route::get('/shop/filtered', function (Request $request) {
    return ProductController::filteredShop($request);
})->name('filteredShop');

require __DIR__ . '/auth.php';

Route::get('updateModels/{brand}', function ($brand) {
    return ProductController::getModels($brand);
});

Route::get('updateSpecifications/{brand}/{specs}', function ($brand, $specs) {
    return ProductController::getSpecifications($brand, $specs);
});

Route::get('product/{query}', function ($query) {
    return ProductController::product($query);
});
