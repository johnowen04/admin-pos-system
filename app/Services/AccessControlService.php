<?php

namespace App\Services;

use App\Models\User;

class AccessControlService
{
    protected $user = null;
    protected $permissions = [];

    public function __construct(?User $user = null)
    {
        if ($user) {
            $this->setUser($user);
        }
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->loadPermissions();
        return $this;
    }

    protected function loadPermissions()
    {
        if ($this->user) {
            try {
                $this->permissions = $this->user->permissions()->pluck('slug')->toArray();
            } catch (\Exception $e) {
                $this->permissions = [];
            }
        } else {
            $this->permissions = [];
        }
    }

    public function hasPermission(string $permissionName): bool
    {
        // If no user or permissions, deny access
        if (!$this->user || empty($this->permissions)) {
            return false;
        }

        // Direct match
        if (in_array($permissionName, $this->permissions)) {
            return true;
        }

        // Check if user has a wildcard permission that covers this permission
        foreach ($this->permissions as $userPermission) {
            if (strpos($userPermission, '*') !== false) {
                $pattern = '/^' . str_replace('*', '.*', $userPermission) . '$/';
                if (preg_match($pattern, $permissionName)) {
                    return true;
                }
            }
        }

        // Check if the requested permission is a wildcard that matches any user permission
        if (strpos($permissionName, '*') !== false) {
            $prefix = str_replace('*', '', $permissionName);
            foreach ($this->permissions as $userPermission) {
                if (strpos($userPermission, $prefix) === 0) {
                    return true;
                }
            }
        }

        return false;
    }
}
