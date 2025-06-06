<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    /**
     * Get all permissions.
     *
     * @param bool $withTrashed Include soft-deleted permissions
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPermissions($withTrashed = false, $withTrashedFeatures = false, $withTrashedOperations = false, $onlyNonSuperUser = false)
    {
        $query = $withTrashed ? Permission::withTrashed() : Permission::query();

        if ($onlyNonSuperUser) {
            $query->where('is_super_user_only', 0);
        }

        $query->with([
            'feature' => function ($query) use ($withTrashedFeatures) {
                if ($withTrashedFeatures) {
                    $query->withTrashed();
                }
            },
            'operation' => function ($query) use ($withTrashedOperations) {
                if ($withTrashedOperations) {
                    $query->withTrashed();
                }
            }
        ]);

        if (!$withTrashedFeatures) {
            $query->whereHas('feature');
        }

        if (!$withTrashedOperations) {
            $query->whereHas('operation');
        }

        return $query->get();
    }

    /**
     * Create a new permission.
     *
     * @param array $data
     * @return \App\Models\Permission
     */
    public function createPermission(array $data)
    {
        $existingPermission = Permission::withTrashed()
            ->where('feature_id', $data['feature_id'])
            ->where('operation_id', $data['operation_id'])
            ->first();

        if ($existingPermission) {
            if ($existingPermission->trashed()) {
                $existingPermission->restore();
                $existingPermission->update($data);
                return $existingPermission;
            } else {
                return $existingPermission;
            }
        }

        return Permission::create($data);
    }

    /**
     * Create a batch of permissions.
     *
     * @param array $data
     * @return void
     */
    public function createPermissionBatch(array $data)
    {
        return DB::transaction(function () use ($data) {
            foreach ($data as $permission) {

                $this->createPermission($permission);
            }
        });
    }

    /**
     * Update an existing permission.
     *
     * @param \App\Models\Permission $permission
     * @param array $data
     * @return bool
     */
    public function updatePermission(Permission $permission, array $data)
    {
        return $permission->update($data);
    }

    /**
     * Soft delete a permission.
     *
     * @param \App\Models\Permission $permission
     * @return bool|null
     */
    public function deletePermission(Permission $permission)
    {
        return $permission->delete();
    }
}
