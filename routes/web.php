<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Dotenv\Util\Str;
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
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/orderdetails', function () {
    return view('orderdetails');
})->middleware(['auth'])->name('orderdetails');


Route::get('/orders', function () {
    return view('orders');
})->middleware(['auth'])->name('orders');

Route::get('/shop', function (Request $request) {
    return ProductController::shop($request);
});

Route::get('/shop/filtered', function (Request $request) {
    return ProductController::filteredShop($request);
});

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
