<?php

namespace App\Http\Middleware;

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
        if (!$request->user() || !$request->user()->employee) {
            abort(401, 'Unauthorized');
        }

        // Check if there are multiple permissions (using | separator)
        if (strpos($permission, '|') !== false) {
            $permissions = explode('|', $permission);
            foreach ($permissions as $singlePermission) {
                if (Gate::allows($singlePermission)) {
                    return $next($request);
                }
            }
            abort(403, 'Forbidden');
        }

        if (!Gate::allows($permission)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
