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
        $this->middleware('permission:bu.view')->only(['index', 'show']);
        $this->middleware('permission:bu.create')->only(['create', 'store']);
        $this->middleware('permission:bu.edit')->only(['edit', 'update']);
        $this->middleware('permission:bu.delete')->only(['destroy']);

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
            'createRoute' => route('baseunit.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bu.create', [
            'action' => route('baseunit.store'),
            'method' => 'POST',
            'baseUnit' => null,
            'cancelRoute' => route('baseunit.index'),
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
        return redirect()->route('baseunit.index')->with('success', 'Base Unit created successfully.');
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
    public function edit(BaseUnit $baseUnit)
    {
        return view('bu.edit', [
            'action' => route('baseunit.update', $baseUnit->id),
            'method' => 'PUT',
            'baseUnit' => $baseUnit,
            'cancelRoute' => route('baseunit.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BaseUnit $baseUnit)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:45',
        ]);

        // Update the base unit
        $this->baseUnitService->updateBaseUnit($baseUnit, $validatedData);

        // Redirect back to the base unit index with a success message
        return redirect()->route('baseunit.index')->with('success', 'Base Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BaseUnit $baseUnit)
    {
        // Perform soft delete
        $this->baseUnitService->deleteBaseUnit($baseUnit);

        // Redirect back to the base unit index with a success message
        return redirect()->route('baseunit.index')->with('success', 'Base Unit deleted successfully.');
    }
}
