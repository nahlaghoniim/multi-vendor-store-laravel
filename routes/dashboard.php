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
use App\Http\Controllers\Dashboard\OrdersController;
use App\Http\Controllers\Dashboard\StoreController;

Route::group([
    'middleware' => ['auth:admin,web'],
    'as' => 'dashboard.',
    'prefix' => 'admin/dashboard', 
], 
function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    
    // Notification mark as read route
  Route::post('/notifications/{id}/read', function($id) {
    try {
        // Get authenticated admin
        $admin = auth()->guard('admin')->user();
        
        if (!$admin) {
            $admin = auth()->user();
        }
        
        if (!$admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Use DatabaseNotification model directly
        $notification = \Illuminate\Notifications\DatabaseNotification::where('id', $id)
            ->where('notifiable_type', get_class($admin))
            ->where('notifiable_id', $admin->id)
            ->first();
        
        if (!$notification) {
            \Log::error('Notification not found', [
                'notification_id' => $id,
                'admin_id' => $admin->id,
                'admin_class' => get_class($admin)
            ]);
            return response()->json(['error' => 'Notification not found'], 404);
        }
        
        $notification->markAsRead();
        
        \Log::info('Notification marked as read successfully', [
            'notification_id' => $id
        ]);
        
        return response()->json(['success' => true]);
        
    } catch (\Exception $e) {
        \Log::error('Error marking notification as read', [
            'error' => $e->getMessage(),
            'line' => $e->getLine()
        ]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('notifications.read');
    
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
    
    Route::resource('stores', \App\Http\Controllers\Dashboard\StoreController::class);
      Route::post('stores/{store}/add-product', [StoreController::class, 'addProduct'])
        ->name('stores.addProduct');
    Route::delete('stores/{store}/remove-product/{product}', [StoreController::class, 'removeProduct'])
        ->name('stores.removeProduct');
    
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