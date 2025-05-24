<?php

namespace App\Services;

use App\Enums\PositionLevel;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccessControlService
{
    protected ?User $user = null;
    protected array $permissions = [];

    public function __construct(?User $user = null)
    {
        if ($user) {
            $this->setUser($user);
        }
    }

    public function getUser()
    {
        if ($this->hasUser()) {
            return $this->user;
        }
        $this->setUser(Auth::user());
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->loadPermissions();
        return $this;
    }

    public function hasUser(): bool
    {
        return !is_null($this->user);
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

    public function isSuperUser(): bool
    {
        if (!$this->getUser()) {
            return false;
        }

        // Adjust this to match your exact superuser detection logic
        return !$this->user->employee && ($this->user->role && $this->user->role->name === 'Super User');
    }

    public function isSuperUserOnly(string $permissionSlug): bool
    {
        // Cache permission model lookup if needed for performance

        $permModel = Permission::where('slug', $permissionSlug)->first();

        return $permModel && !empty($permModel->is_super_user_only);
    }

    public function hasPermission(string $permissionName): bool
    {
        if (!$this->user || empty($this->permissions)) {
            return false;
        }

        if ($this->isSuperUser()) {
            // Superusers bypass permission checks
            return true;
        }

        // Direct match
        if (in_array($permissionName, $this->permissions)) {
            return true;
        }

        // Wildcard matching
        foreach ($this->permissions as $userPermission) {
            if (strpos($userPermission, '*') !== false) {
                $pattern = '/^' . str_replace(['.', '*'], ['\.', '[^.]*'], $userPermission) . '$/';
                if (preg_match($pattern, $permissionName)) {
                    return true;
                }
            }
        }

        // If requested permission is a wildcard that matches any user permission
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

    public function getCurrentUserLevel()
    {
        if (!$this->user) {
            return 0;
        }

        try {
            if ($this->isSuperUser()) {
                return 100;
            } else if ($this->user->employee && $this->user->employee->position) {
                $position = $this->user->employee->position;
                if ($position->level instanceof PositionLevel) {
                    return $position->level->value;
                } else if (is_numeric($position->level)) {
                    return (int)$position->level;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting position level: ' . $e->getMessage());
        }
    }
}
