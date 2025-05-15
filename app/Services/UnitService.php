<?php

namespace App\Services;

use App\Models\Unit;

class UnitService
{
    /**
     * Get all units.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUnits()
    {
        return Unit::all();
    }

    /**
     * Create a new unit.
     *
     * @param array $data
     * @return \App\Models\Unit
     */
    public function createUnit(array $data)
    {
        return Unit::create($data);
    }

    /**
     * Update an existing unit.
     *
     * @param \App\Models\Unit $unit
     * @param array $data
     * @return bool
     */
    public function updateUnit(Unit $unit, array $data)
    {
        return $unit->update($data);
    }

    /**
     * Delete a unit (soft delete).
     *
     * @param \App\Models\Unit $unit
     * @return bool|null
     */
    public function deleteUnit(Unit $unit)
    {
        return $unit->delete();
    }
}