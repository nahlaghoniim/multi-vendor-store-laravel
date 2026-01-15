<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\CategoriesController;
use App\Http\Controllers\Dashboard\ProductsController;
use App\Http\Controllers\Dashboard\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\RolesController;
use App\Http\Controllers\Dashboard\UsersController;
use App\Http\Controllers\Dashboard\AdminsController;
use App\Http\Controllers\Dashboard\ImportProductsController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\OrdersController;

Route::group([
    'middleware' => ['auth:admin,web'],
    'as' => 'dashboard.',
    'prefix' => 'dashboard', // Changed from 'admin/dashboard'
], 
function () {

    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::resource('roles', RolesController::class);

    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Categories
    Route::get('categories/trash', [CategoriesController::class, 'trash'])->name('categories.trash');
    Route::put('categories/{category}/restore', [CategoriesController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{category}/force-delete', [CategoriesController::class, 'forceDelete'])->name('categories.force-delete');
    Route::resource('categories', CategoriesController::class);

    // Products Import (must come BEFORE resource)
    Route::get('products/import', [ImportProductsController::class, 'create'])
        ->name('products.import');
    Route::post('products/import', [ImportProductsController::class, 'store']);
    
    // Products Resource
    Route::resource('products', ProductsController::class);
    
    // Orders
    Route::resource('orders', OrdersController::class)
        ->only(['index', 'show']);

    Route::patch('orders/{order}/status', [OrdersController::class, 'updateStatus'])
        ->name('orders.update-status');

    // Users
    Route::resource('users', UsersController::class);

    // Admins
    Route::resource('admins', AdminsController::class);
        
});