<?php

namespace App\Providers;

use App\Models\Product;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
        // Add other models here
    ];

    /**
     * Register any application services.
     */
    public function register()
    {
        // Optional: bind abilities if you need
        $this->app->bind('abilities', function() {
            return include base_path('data/abilities.php');
        });
    }

    /**
     * Register any authentication / authorization services.
     */
   public function boot()
{
    $this->registerPolicies();

    Gate::before(function ($user, $ability) {
        if (auth('admin')->check()) {
            return true;
        }

        if (property_exists($user, 'super_admin') && $user->super_admin) {
            return true;
        }

        return null;
    });

    foreach ($this->app->make('abilities') as $code => $label) {
        Gate::define($code, function ($user) use ($code) {
            if (auth('admin')->check()) {
                return true;
            }

            return method_exists($user, 'hasAbility')
                && $user->hasAbility($code);
        });
    }
}
}