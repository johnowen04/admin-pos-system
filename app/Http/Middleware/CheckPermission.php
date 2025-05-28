<?php

namespace App\Http\Middleware;

use App\Services\AccessControlService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    protected AccessControlService $accessControl;

    public function __construct(AccessControlService $accessControl)
    {
        $this->accessControl = $accessControl;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $this->accessControl->getUser();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $permissions = explode('|', $permission);

        if ($this->accessControl->isSuperUser()) {
            return $next($request);
        }

        foreach ($permissions as $perm) {
            if ($this->accessControl->isSuperUserOnly($perm)) {
                abort(403, 'This feature is restricted to Super Users only');
            }
        }

        foreach ($permissions as $perm) {
            if ($this->accessControl->hasPermission($perm)) {
                return $next($request);
            }
        }

        abort(403, 'Forbidden - You do not have the required permissions');
    }
}
