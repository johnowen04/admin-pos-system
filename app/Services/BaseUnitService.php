<?php

namespace App\Services;

use App\Models\BaseUnit;

class BaseUnitService
{
    /**
     * Get all base units.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBaseUnits()
    {
        return BaseUnit::all();
    }

    /**
     * Create a new base unit.
     *
     * @param array $data
     * @return \App\Models\BaseUnit
     */
    public function createBaseUnit(array $data)
    {
        return BaseUnit::create($data);
    }

    /**
     * Update an existing base unit.
     *
     * @param \App\Models\BaseUnit $baseUnit
     * @param array $data
     * @return bool
     */
    public function updateBaseUnit(BaseUnit $baseUnit, array $data)
    {
        return $baseUnit->update($data);
    }

    /**
     * Delete a base unit (soft delete).
     *
     * @param \App\Models\BaseUnit $baseUnit
     * @return bool|null
     */
    public function deleteBaseUnit(BaseUnit $baseUnit)
    {
        return $baseUnit->delete();
    }
}