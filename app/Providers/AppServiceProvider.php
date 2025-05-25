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
        $this->app->singleton(AccessControlService::class, function ($app) {
            return new AccessControlService();
        });

        $this->app->singleton(OutletService::class, function ($app) {
            return new OutletService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $accessControl = app(AccessControlService::class);
                $outletService = app(OutletService::class);

                if ($accessControl->isSuperUser()) {
                    $outlets = $outletService->getAllOutlets();
                } else {
                    $outlets = $accessControl->getUser()->employee->outlets;
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
