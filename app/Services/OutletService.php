<?php

namespace App\Services;

use App\Models\Outlet;

class OutletService
{
    /**
     * Get all outlets.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllOutlets()
    {
        return Outlet::all();
    }

    /**
     * Get an outlet by its ID.
     *
     * @param int $id
     * @return \App\Models\Outlet|null
     */
    public function getOutletById(int $id)
    {
        return Outlet::where('id', $id);
    }

    /**
     * Create a new outlet.
     *
     * @param array $data
     * @return \App\Models\Outlet
     */
    public function createOutlet(array $data)
    {
        return Outlet::create($data);
    }

    /**
     * Update an existing outlet.
     *
     * @param \App\Models\Outlet $outlet
     * @param array $data
     * @return bool
     */
    public function updateOutlet(Outlet $outlet, array $data)
    {
        return $outlet->update($data);
    }

    /**
     * Delete an outlet (soft delete).
     *
     * @param \App\Models\Outlet $outlet
     * @return bool|null
     */
    public function deleteOutlet(Outlet $outlet)
    {
        return $outlet->delete();
    }
}