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
        return in_array($permissionName, $this->permissions);
    }
}