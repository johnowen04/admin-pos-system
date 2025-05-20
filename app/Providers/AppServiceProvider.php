<?php

namespace App\Providers;

use App\Models\Employee;
use App\Services\AccessControlService;
use Illuminate\Support\Facades\Auth;
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
            $user = Auth::user();
            if (!$user || !$user->employee) {
                return new AccessControlService(new Employee());
            }
            return new AccessControlService($user->employee);
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
