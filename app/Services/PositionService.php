<?php

namespace App\Services;

use App\Models\Position;
use Illuminate\Support\Facades\DB;

class PositionService
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
     * Get all positions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPositions($withTrashed = false)
    {
        return $withTrashed ? Position::withTrashed()->get() : Position::all();
    }

    public function getPositionByName(string $positionName)
    {
        return Position::where('name', $positionName)->first();
    }

    public function getAllPositionWithLowerOrEqualLevel(int $level)
    {
        return Position::where('level', '<=', $level)->get();
    }

    /**
     * Create a new position.
     *
     * @param array $data
     * @return \App\Models\Position
     */
    public function createPosition(array $data, array $permissionIds = [])
    {
        $existingPosition = Position::withTrashed()
            ->where('name', $data['name'])
            ->first();

        if ($existingPosition) {
            if ($existingPosition->trashed()) {
                $existingPosition->restore();

                if (!empty($permissionIds)) {
                    $existingPosition->permissions()->sync($permissionIds);
                } else {
                    $existingPosition->permissions()->detach();
                }
                return $existingPosition;
            } else {
                return $existingPosition;
            }
        }

        $position = Position::create($data);

        if (!empty($permissionIds)) {
            $position->permissions()->sync($permissionIds);
        }

        return $position;
    }

    /**
     * Update a position and sync its permissions.
     *
     * @param \App\Models\Position $position
     * @param array $data Position data
     * @param array $permissionIds Permission IDs to sync
     * @return \App\Models\Position
     */
    public function updatePosition(Position $position, array $data, array $permissionIds = [])
    {
        $position->update($data);

        if (!empty($permissionIds)) {
            $position->permissions()->sync($permissionIds);
        } else {
            $position->permissions()->detach();
        }

        return $position;
    }

    /**
     * Delete a position.
     *
     * @param \App\Models\Position $position
     * @return bool|null
     */
    public function deletePosition(Position $position)
    {
        return $position->delete();
    }

    /**
     * Update permissions for multiple positions from ACL matrix.
     *
     * @param array $permissionsData The permissions data from ACL matrix form
     * @return array Associative array with updated position names as keys
     * @throws \Exception If any part of the update fails
     */
    public function updateACLMatrix(array $permissionsData)
    {
        $updatedPositions = [];

        DB::beginTransaction();

        try {
            foreach ($permissionsData as $positionName => $features) {
                // Find the position by name
                $position = $this->getPositionByName($positionName);

                if (!$position) {
                    continue;
                }

                $permissionIds = [];

                foreach ($features as $featureName => $operations) {
                    foreach ($operations as $operationName => $value) {
                        if ($value == '1') {
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

                $position->permissions()->sync($permissionIds);
                $updatedPositions[$positionName] = count($permissionIds);
            }

            DB::commit();
            return $updatedPositions;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
