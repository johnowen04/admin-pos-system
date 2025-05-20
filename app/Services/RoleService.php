<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;

class RoleService
{
    /**
     * Get all roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles($withTrashed = false)
    {
        return $withTrashed ? Role::withTrashed()->get() : Role::all();
    }

    public function getRoleByName(string $roleName)
    {
        return Role::where('name', $roleName)->first();
    }

    /**
     * Create a new role.
     *
     * @param array $data
     * @return \App\Models\Role
     */
    public function createRole(array $data)
    {
        $existingRole = Role::withTrashed()
            ->where('name', $data['name'])
            ->first();

        if ($existingRole) {
            if ($existingRole->trashed()) {
                $existingRole->restore();
                $existingRole->update($data);
                return $existingRole;
            } else {
                return $existingRole;
            }
        }

        $role = Role::create($data);
        return $role;
    }

    /**
     * Update a role and sync its permissions.
     *
     * @param \App\Models\Role $role
     * @param array $data Role data
     * @param array $permissionIds Permission IDs to sync
     * @return \App\Models\Role
     */
    public function updateRole(Role $role, array $data)
    {
        return $role->update($data);
    }

    /**
     * Delete a role.
     *
     * @param \App\Models\Role $role
     * @return bool|null
     */
    public function deleteRole(Role $role)
    {
        $usersWithRole = User::where('role_id', $role->id)->count();

        if ($usersWithRole > 0) {
            throw new \Exception("This role is assigned to {$usersWithRole} user(s) and cannot be deleted.");
        }
        return $role->delete();
    }
}
