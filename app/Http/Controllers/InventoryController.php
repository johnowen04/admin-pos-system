<?php

namespace App\Http\Controllers;

use App\Services\StockMovementService;
use App\ViewModels\InventoryViewModel;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {
        $this->middleware('permission:inventory.view|inventory.*')->only(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'non-zero'); // default to non-zero
        $data = $this->getProductWithMovementsData();
        $viewModel = new InventoryViewModel($data, $filter);
        return view('inventory.index', ['inventory' => $viewModel]);
    }

    /**
     * Get product with stock movements data with outlet filter.
     */
    private function getProductWithMovementsData()
    {
        $selectedOutletId = session('selected_outlet_id', null);

        if (!$selectedOutletId || $selectedOutletId === 'all') {
            return $this->stockMovementService->getProductsWithMovements();
        }

        return $this->stockMovementService->getProductsWithMovements($selectedOutletId);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
