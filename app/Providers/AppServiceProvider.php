<?php

namespace App\Providers;

use App\Services\AccessControlService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register AccessControlService as a singleton
        $this->app->singleton('accessControl', function ($app) {
            return new AccessControlService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
