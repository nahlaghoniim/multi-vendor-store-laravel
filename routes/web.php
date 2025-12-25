<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\ProductsController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CurrencyConverterController;
use App\Http\Controllers\Front\CheckoutController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductsController::class, 'show'])->name('products.show');

Route::resource('cart', CartController::class);

Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store']);
Route::post('/currency/change', [CurrencyConverterController::class, 'store'])
    ->name('currency.change');Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
