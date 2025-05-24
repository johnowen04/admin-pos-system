<?php

namespace App\Providers;

use App\Services\AccessControlService;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Register a gate check handler for all permissions
        Gate::before(function ($user, $ability) {
            if (!$user) {
                return false;
            }

            $accessControl = app(AccessControlService::class)->setUser($user);
            if ($accessControl->hasPermission($ability)) {
                return true;
            }

            // Return null to continue checking other gates
            return null;
        });
    }
}
