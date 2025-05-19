<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;
use App\Services\OutletService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public $productService;
    public $outletService;
    public $inventoryService;

    public function __construct(ProductService $productService, OutletService $outletService, InventoryService $inventoryService)
    {
        $this->middleware('permission:inventory.view')->only(['index', 'show']);

        $this->productService = $productService;
        $this->outletService = $outletService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->productService->getProductsWithMovements();
        $groupedGlobal = [];
        $groupedDetail = [];

        foreach ($products as $product) {
            $productId = $product->id; // Or $product->sku

            foreach ($product->movements as $movement) {
                $movementType = $movement->movement_type->value ?? 'unknown';
                $outletId = $movement->outlet_id ?? 'unknown';

                // Grouped Global
                $groupedGlobal[$productId][$movementType][$outletId][] = $movement;

                // Grouped Detail
                $groupedDetail[$productId][$outletId]['name'] = $movement->outlet->name ?? 'Unknown Outlet';
                $groupedDetail[$productId][$outletId][$movementType][] = $movement;
            }
        }

        return view('inventory.index', [
            'products' => $products,
            'groupedGlobal' => $groupedGlobal,
            'groupedDetail' => $groupedDetail,
            'createRoute' => route('inventory.create'),
        ]);
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
