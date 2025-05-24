<?php

namespace App\Http\Controllers;

use App\Models\BaseUnit;
use App\Services\BaseUnitService;
use Illuminate\Http\Request;

class BaseUnitController extends Controller
{
    protected $baseUnitService;

    /**
     * Constructor to inject the BaseUnitService.
     */
    public function __construct(BaseUnitService $baseUnitService)
    {
        $this->middleware('permission:bu.view|bu.*')->only(['index', 'show']);
        $this->middleware('permission:bu.create|bu.*')->only(['create', 'store']);
        $this->middleware('permission:bu.edit|bu.*')->only(['edit', 'update']);
        $this->middleware('permission:bu.delete|bu.*')->only(['destroy']);

        $this->baseUnitService = $baseUnitService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $baseUnits = $this->baseUnitService->getAllBaseUnits();
        return view('bu.index', [
            'baseUnits' => $baseUnits, // Placeholder for base units
            'createRoute' => route('bu.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bu.create', [
            'action' => route('bu.store'),
            'method' => 'POST',
            'baseUnit' => null,
            'cancelRoute' => route('bu.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:45',
        ]);

        // Create the base unit
        $this->baseUnitService->createBaseUnit($validatedData);

        // Redirect back to the base unit index with a success message
        return redirect()->route('bu.index')->with('success', 'Base Unit created successfully.');
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
    public function edit(BaseUnit $bu)
    {
        return view('bu.edit', [
            'action' => route('bu.update', $bu->id),
            'method' => 'PUT',
            'baseUnit' => $bu,
            'cancelRoute' => route('bu.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BaseUnit $bu)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:45',
        ]);

        // Update the base unit
        $this->baseUnitService->updateBaseUnit($bu, $validatedData);

        // Redirect back to the base unit index with a success message
        return redirect()->route('bu.index')->with('success', 'Base Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BaseUnit $bu)
    {
        // Perform soft delete
        $this->baseUnitService->deleteBaseUnit($bu);

        // Redirect back to the base unit index with a success message
        return redirect()->route('bu.index')->with('success', 'Base Unit deleted successfully.');
    }
}
