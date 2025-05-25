<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Services\OutletService;

class OutletController extends Controller
{
    /**
     * Constructor to inject the OutletService.
     */
    public function __construct(protected OutletService $outletService)
    {
        $this->middleware('permission:outlet.view|outlet.*')->only(['index', 'show']);
        $this->middleware('permission:outlet.create|outlet.*')->only(['create', 'store']);
        $this->middleware('permission:outlet.edit|outlet.*')->only(['edit', 'update']);
        $this->middleware('permission:outlet.delete|outlet.*')->only(['destroy']);
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

        $this->outletService->createOutlet($validatedData);
        return redirect()->route('outlet.index')->with('success', 'Outlet created successfully.');
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

        $this->outletService->updateOutlet($outlet, $validatedData);
        return redirect()->route('outlet.index')->with('success', 'Outlet updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Outlet $outlet)
    {
        $this->outletService->deleteOutlet($outlet);
        return redirect()->route('outlet.index')->with('success', 'Outlet deleted successfully.');
    }
}
