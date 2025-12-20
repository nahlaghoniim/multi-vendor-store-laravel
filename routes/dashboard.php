<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\CategoriesController;
use App\Http\Controllers\Dashboard\ProductsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth'],
    'as' => 'dashboard.',
    'prefix' => 'dashboard',
], function () {

    Route::get('/', [DashboardController::class, 'index'])
        ->name('index');
Route::get('categories/trash', [CategoriesController::class, 'trash'])
    ->name('categories.trash');

Route::put('categories/{category}/restore', [CategoriesController::class, 'restore'])
    ->name('categories.restore');

Route::delete('categories/{category}/force-delete', [CategoriesController::class, 'forceDelete'])
    ->name('categories.force-delete');

// Categories (RESOURCE)
Route::resource('categories', CategoriesController::class);
Route::resource('products', ProductsController::class);
Route::get('/orders', function () {
    return view('dashboard.orders.index');  // or any view name you want
})->name('orders.index');
Route::get('/roles', function () {
    return view('dashboard.roles.index'); // or any temporary view
})->name('roles.index');
Route::get('/users', function () {
    return view('dashboard.users.index'); // or any temporary view
})->name('users.index');

    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
});
