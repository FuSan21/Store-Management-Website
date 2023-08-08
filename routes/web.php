<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
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
    return view('dashboard')->with('pageName', 'Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/orderdetails/{orderNo}', function ($orderNo) {
    return OrderController::orderDetails($orderNo);
})->middleware(['auth'])->name('orderdetails');

Route::get('/usersetting', function () {
    return view('usersetting');
})->middleware(['auth'])->name('usersetting');


Route::get('/orders', function () {
    return OrderController::index();
})->middleware(['auth'])->name('orders');

Route::get('/shop', [ProductController::class, 'shop']);

require __DIR__ . '/auth.php';

Route::get('updateModels/{brand}', function ($brand) {
    return ProductController::getModels($brand);
});

Route::get('updateSpecifications/{brand}/{specs}', function ($brand, $specs) {
    return ProductController::getSpecifications($brand, $specs);
});
