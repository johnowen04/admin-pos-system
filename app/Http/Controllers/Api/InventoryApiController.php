<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    /**
     * Constructor to inject services.
     */
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * API to Get products by outlet ID.
     */
    public function getProductsByOutlet(int $outletId)
    {
        $products = $this->inventoryService->getStocksByOutlet($outletId);
        return response()->json(['products' => $products]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
