<?php

namespace App\Services;

use App\Models\Position;

class PositionService
{
    /**
     * Get all positions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPositions($withTrashed = false)
    {
        return $withTrashed ? Position::withTrashed()->get() : Position::all();
    }

    /**
     * Get a position by its name.
     * 
     * @param string $positionName
     * @return \App\Models\Position|null
     */
    public function getPositionByName(string $positionName)
    {
        return Position::where('name', $positionName)->first();
    }

    /**
     * Get all positions with a level lower than or equal to the specified level.
     * 
     * @param int $level
     * @return \Illuminate\Database\Eloquent\Collection
     */
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
    public function createPosition(array $data)
    {
        $existingPosition = Position::withTrashed()
            ->where('name', $data['name'])
            ->first();

        $permissions = $data['permissions'] ?? [];

        if ($existingPosition) {
            if ($existingPosition->trashed()) {
                $existingPosition->restore();
                $existingPosition->permissions()->sync($permissions);
                return $existingPosition;
            } else {
                return $existingPosition;
            }
        }

        $position = Position::create($data);
        $position->permissions()->sync($permissions);
        return $position;
    }

    /**
     * Update a position and sync its permissions.
     *
     * @param \App\Models\Position $position
     * @param array $data Position data
     * @return \App\Models\Position
     */
    public function updatePosition(Position $position, array $data)
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        if (!empty($data)) {
            $position->update($data);
        }

        $position->permissions()->sync($permissions);

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
}
