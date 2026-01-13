<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\Auth\TwoFactorAuthenticationController;
use App\Http\Controllers\Front\ProductsController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CurrencyConverterController;
use App\Http\Controllers\Front\CheckoutController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Front\PaymentsController;
use App\Http\Controllers\Front\OrdersController;
use App\Http\Controllers\StripeWebhooksController;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
], function() {

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductsController::class, 'show'])->name('products.show');

Route::resource('cart', CartController::class);

 Route::get('checkout', [CheckoutController::class, 'create'])->name('checkout');
    Route::post('checkout', [CheckoutController::class, 'store']);
  
Route::post('/currency/change', [CurrencyConverterController::class, 'store'])
    ->name('currency.change');Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/auth/user/2fa', [TwoFactorAuthenticationController::class, 'index'])
    ->name('front.2fa');
   // Redirect user to Google
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');

// Handle callback from Google
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::get('login/facebook', [SocialAuthController::class, 'redirectToFacebook']);
Route::get('login/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);
Route::prefix(LaravelLocalization::setLocale())->group(function () {

    // Show payment page
    Route::get('/orders/{order}/payments/create', [PaymentsController::class, 'create'])
        ->name('orders.payments.create');

    // Create Stripe PaymentIntent
   Route::post('orders/{order}/payments/stripe-intent', [PaymentsController::class, 'stripeIntent'])
    ->name('stripe.paymentIntent.create');

    // Confirm payment (redirect after success)
    Route::get('/orders/{order}/payments/confirm', [PaymentsController::class, 'confirm'])
        ->name('orders.payments.confirm');
        Route::post('/orders', [OrdersController::class, 'store'])->name('orders.store');
        Route::post('/stripe/webhook', [StripeWebhooksController::class, 'handle']);


});

//require __DIR__.'/auth.php';
require __DIR__ . '/dashboard.php';
});