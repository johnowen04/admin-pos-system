<?php

namespace App\Http\Middleware;

use App\Services\AccessControlService;
use Closure;
use Illuminate\Http\Request;
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
        $employee = $request->user()->employee; // Assuming 'user' is your authenticated Employee

        if (!$employee) {
            abort(401, 'Unauthorized');
        }

        $accessControl = new AccessControlService($employee);
        
        // Check if there are multiple permissions (using | separator)
        if (strpos($permission, '|') !== false) {
            $permissions = explode('|', $permission);
            foreach ($permissions as $singlePermission) {
                if ($accessControl->hasPermission($singlePermission)) {
                    return $next($request);
                }
            }
            abort(403, 'Forbidden');
        }

        if (!$accessControl->hasPermission($permission)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
