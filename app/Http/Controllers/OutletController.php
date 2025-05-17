<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Services\OutletService;

class OutletController extends Controller
{
    protected $outletService;

    /**
     * Constructor to inject the OutletService.
     */
    public function __construct(OutletService $outletService)
    {
        $this->outletService = $outletService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $outlets = $this->outletService->getAllOutlets();
        return view('outlet.index', [
            'outlets' => $outlets,
            'createRoute' => route('outlet.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('outlet.create', [
            'action' => route('outlet.store'),
            'method' => 'POST',
            'outlet' => null,
            'cancelRoute' => route('outlet.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:pos,warehouse',
            'status' => 'required|string|in:open,closed',
            'phone' => 'nullable|string|max:15',
            'whatsapp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        // Use the service to create the outlet
        $this->outletService->createOutlet($validatedData);

        // Redirect back to the outlet index with a success message
        return redirect()->route('outlet.index')->with('success', 'Outlet created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Outlet $outlet)
    {
        return view('outlet.edit', [
            'action' => route('outlet.update', $outlet->id),
            'method' => 'PUT',
            'outlet' => $outlet,
            'cancelRoute' => route('outlet.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Outlet $outlet)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:pos,warehouse',
            'status' => 'required|string|in:open,closed',
            'phone' => 'nullable|string|max:15',
            'whatsapp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        // Use the service to update the outlet
        $this->outletService->updateOutlet($outlet, $validatedData);

        // Redirect back to the outlet index with a success message
        return redirect()->route('outlet.index')->with('success', 'Outlet updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Outlet $outlet)
    {
        // Use the service to delete the outlet
        $this->outletService->deleteOutlet($outlet);

        // Redirect back to the outlet index with a success message
        return redirect()->route('outlet.index')->with('success', 'Outlet deleted successfully.');
    }

    /**
     * API to Get products by outlet ID.
     */
    public function getProductsByOutlet(int $outlet_Id)
    {
        $products = $this->outletService->getProductsWithStocksFromOutlet($outlet_Id);
        return response()->json(['products' => $products]);
    }
}
