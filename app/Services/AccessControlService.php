<?php

namespace App\Services;

use App\Models\Employee;

class AccessControlService
{
    protected Employee $employee;
    protected $permissions;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
        $this->loadPermissions();
    }

    protected function loadPermissions()
    {
        // Load permissions for employee roles - eager load roles & permissions
        $this->permissions = $this->employee->role()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('slug')
            ->unique()
            ->toArray();
    }

    public function hasPermission(string $permissionName): bool
    {
        // Direct match - existing functionality
        if (in_array($permissionName, $this->permissions)) {
            return true;
        }

        // Handle wildcard permissions (like acl.*)
        if (strpos($permissionName, '*') !== false) {
            $prefix = str_replace('*', '', $permissionName);

            foreach ($this->permissions as $userPermission) {
                if (strpos($userPermission, $prefix) === 0) {
                    return true;
                }
            }
        }

        // Handle regular permissions that might match a wildcard the user has
        foreach ($this->permissions as $userPermission) {
            if (strpos($userPermission, '*') !== false) {
                $pattern = '/^' . str_replace('*', '.*', $userPermission) . '$/';
                if (preg_match($pattern, $permissionName)) {
                    return true;
                }
            }
        }

        return false;
    }
}
