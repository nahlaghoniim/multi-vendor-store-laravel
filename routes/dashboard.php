<?php
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\CategoriesController;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => ['auth'], // use default web guard
    'as' => 'dashboard.',
    'prefix' => 'dashboard',   // simpler URL
], function () {

    // Profile routes
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Dashboard home
Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Categories resource + extra routes
    Route::resource('categories', CategoriesController::class);
    Route::get('categories/trash', [CategoriesController::class, 'trash'])->name('categories.trash');
    Route::put('categories/{category}/restore', [CategoriesController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{category}/force-delete', [CategoriesController::class, 'forceDelete'])->name('categories.force-delete');
});
