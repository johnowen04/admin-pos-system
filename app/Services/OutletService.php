<?php

namespace App\Services;

use App\Models\Outlet;

class OutletService
{
    public $outletService;
    public $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

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
        return Outlet::find($id);
    }

    
    public function getProductsByOutletId(int $id)
    {
        $outlet = $this->getOutletById($id);
        if (!$outlet) {
            return null;
        }
        return $outlet->products;
    }

    public function getProductsWithStocksFromOutlet(int $outletId)
    {
        $products = $this->inventoryService->getStocksByOutlet($outletId); // Fetch all products

        return $products;
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