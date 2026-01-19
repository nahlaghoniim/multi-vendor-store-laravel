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


require __DIR__ . '/dashboard.php';


Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
        \App\Http\Middleware\SetAppLocale::class,
    ],
], function () {
    
    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    // Products
    Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [ProductsController::class, 'show'])->name('products.show');
    
    // Cart
    Route::resource('cart', CartController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    
    // Checkout
    Route::get('checkout', [CheckoutController::class, 'create'])->name('checkout');
    Route::post('checkout', [CheckoutController::class, 'store']);
    
    // Currency
    Route::post('/currency/change', [CurrencyConverterController::class, 'store'])
        ->name('currency.change');
    
    // Authenticated user routes
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        
        // Orders & Payments
        Route::post('orders', [OrdersController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrdersController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/payments/create', [PaymentsController::class, 'create'])
            ->name('orders.payments.create');
        Route::post('orders/{order}/payments/stripe-intent', [PaymentsController::class, 'stripeIntent'])
            ->name('stripe.paymentIntent.create');
        Route::get('orders/{order}/payments/confirm', [PaymentsController::class, 'confirm'])
            ->name('orders.payments.confirm');
        Route::get('orders/{order}/success', [PaymentsController::class, 'success'])
            ->name('orders.success');
        Route::get('orders/{order}/tracking', [OrdersController::class, 'tracking'])
            ->name('orders.tracking');
    });
    
    // 2FA
   Route::get('/auth/{id}/2fa', [TwoFactorAuthenticationController::class, 'index'])
    ->name('front.2fa');
    
    // Social Auth
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
    Route::get('login/facebook', [SocialAuthController::class, 'redirectToFacebook']);
    Route::get('login/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);
});


Route::post('stripe/webhook', [StripeWebhooksController::class, 'handle'])
    ->name('stripe.webhook');