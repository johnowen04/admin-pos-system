<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user()) {
            abort(401, 'Unauthorized');
        }

        $user = $request->user();
        $isSuperUser = !$user->employee && ($user->role || $user->role->name === 'superuser');

        // For superuser role, allow all actions
        if ($isSuperUser) {
            return $next($request);
        }

        // Check if permission is super-user only
        $permissions = explode('|', $permission);
        foreach ($permissions as $singlePermission) {
            // Get the permission model to check its properties
            $permModel = Permission::where('slug', $singlePermission)->first();

            // If the permission is marked as super-user only, deny access for non-superusers
            if ($permModel && isset($permModel->is_super_user_only) && $permModel->is_super_user_only) {
                abort(403, 'This feature is restricted to Super Users only');
            }
        }

        // Continue with regular permission checks
        if (strpos($permission, '|') !== false) {
            foreach ($permissions as $singlePermission) {
                if (Gate::allows($singlePermission)) {
                    return $next($request);
                }
            }
            abort(403, 'Forbidden - You do not have the required permissions');
        }

        if (!Gate::allows($permission)) {
            abort(403, 'Forbidden - You do not have the required permissions');
        }

        return $next($request);
    }
}
