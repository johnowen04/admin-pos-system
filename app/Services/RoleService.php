<?php

namespace App\Services;

use App\Models\Role;

class RoleService
{
    /**
     * Get all roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles()
    {
        return Role::all();
    }

    /**
     * Create a new role.
     *
     * @param array $data
     * @return \App\Models\Role
     */
    public function createRole(array $data)
    {
        return Role::create($data);
    }

    /**
     * Update an existing role.
     *
     * @param \App\Models\Role $role
     * @param array $data
     * @return bool
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
        return $role->delete();
    }
}