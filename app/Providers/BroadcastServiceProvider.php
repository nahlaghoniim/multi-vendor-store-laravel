<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use admin guard for broadcast authentication
        Broadcast::routes(['middleware' => ['web', 'auth:admin,web']]);

        require base_path('routes/channels.php');
    }
}