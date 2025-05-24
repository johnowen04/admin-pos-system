<?php

namespace App\Providers;

use App\Services\AccessControlService;
use App\Services\InventoryService;
use App\Services\OutletService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register AccessControlService as a singleton
        $this->app->singleton(AccessControlService::class, function ($app) {
            return new AccessControlService();
        });

        $this->app->singleton(InventoryService::class);
        $this->app->singleton(OutletService::class, function ($app) {
            return new OutletService($app->make(InventoryService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                if (app(AccessControlService::class)->isSuperUser()) {
                    $outlets = app(OutletService::class)->getAllOutlets();
                } else {
                    $outlets = app(AccessControlService::class)->getUser()->employee->outlets;
                }
                
                $view->with('outlets', $outlets);
                $view->with('selectedOutlet', session('selected_outlet', $outlets->first()->name ?? null));
                $view->with('selectedOutletId', session('selected_outlet_id', $outlets->first()->id ?? null));
            } else {
                $view->with('outlets', []);
                $view->with('selectedOutlet', null);
                $view->with('selectedOutletId', null);
            }
        });
    }
}
