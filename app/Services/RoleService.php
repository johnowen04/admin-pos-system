<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleService
{

    protected $featureService;
    protected $operationService;
    protected $permissionService;

    public function __construct(
        FeatureService $featureService,
        OperationService $operationService,
        PermissionService $permissionService
    ) {
        $this->featureService = $featureService;
        $this->operationService = $operationService;
        $this->permissionService = $permissionService;
    }

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

    public function getAllRoleWithLowerOrEqualLevel(int $level)
    {
        return Role::where('level', '<=', $level)->get();
    }

    /**
     * Create a new role.
     *
     * @param array $data
     * @return \App\Models\Role
     */
    public function createRole(array $data, array $permissionIds = [])
    {
        // Check if there's a soft-deleted role with the same name
        $existingRole = Role::withTrashed()
            ->where('name', $data['name'])
            ->first();

        if ($existingRole) {
            if ($existingRole->trashed()) {
                // If found and trashed, restore
                $existingRole->restore(); // Sync permissions if any

                if (!empty($permissionIds)) {
                    $existingRole->permissions()->sync($permissionIds);
                } else {
                    $existingRole->permissions()->detach();
                }
                return $existingRole;
            } else {
                return $existingRole;
            }
        }

        // Create the role
        $role = Role::create($data);

        // Sync permissions if any
        if (!empty($permissionIds)) {
            $role->permissions()->sync($permissionIds);
        }

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
    public function updateRole(Role $role, array $data, array $permissionIds = [])
    {
        // Update role data
        $role->update($data);

        // Sync permissions if any
        if (!empty($permissionIds)) {
            $role->permissions()->sync($permissionIds);
        } else {
            $role->permissions()->detach();
        }

        return $role;
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


    /**
     * Update permissions for multiple roles from ACL matrix.
     *
     * @param array $permissionsData The permissions data from ACL matrix form
     * @return array Associative array with updated role names as keys
     * @throws \Exception If any part of the update fails
     */
    public function updateACLMatrix(array $permissionsData)
    {
        $updatedRoles = [];

        // Start the transaction in the service
        DB::beginTransaction();

        try {
            foreach ($permissionsData as $roleName => $features) {
                // Find the role by name
                $role = $this->getRoleByName($roleName);

                if (!$role) {
                    continue;
                }

                $permissionIds = [];

                foreach ($features as $featureName => $operations) {
                    foreach ($operations as $operationName => $value) {
                        if ($value == '1') {
                            // Find the permission by feature and operation
                            $feature = $this->featureService->getAllFeatures()->firstWhere('name', $featureName);
                            $operation = $this->operationService->getAllOperations()->firstWhere('name', $operationName);

                            if (!$feature || !$operation) {
                                continue;
                            }

                            $permission = $this->permissionService->getAllPermissions()->first(function ($p) use ($feature, $operation) {
                                return $p->feature_id == $feature->id && $p->operation_id == $operation->id;
                            });

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                            }
                        }
                    }
                }

                // Sync permissions for this role
                $role->permissions()->sync($permissionIds);
                $updatedRoles[$roleName] = count($permissionIds);
            }

            // Commit the transaction in the service
            DB::commit();
            return $updatedRoles;
        } catch (\Exception $e) {
            // Roll back the transaction in the service
            DB::rollBack();
            // Re-throw the exception so the controller can handle it
            throw $e;
        }
    }
}
