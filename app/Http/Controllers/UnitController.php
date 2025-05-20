<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Services\BaseUnitService;
use App\Services\UnitService;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected $unitService;
    protected $baseUnitService;

    /**
     * Constructor to inject the UnitService.
     */
    public function __construct(BaseUnitService $baseUnitService, UnitService $unitService)
    {
        $this->middleware('permission:unit.view|unit.*')->only(['index', 'show']);
        $this->middleware('permission:unit.create|unit.*')->only(['create', 'store']);
        $this->middleware('permission:unit.edit|unit.*')->only(['edit', 'update']);
        $this->middleware('permission:unit.delete|unit.*')->only(['destroy']);

        $this->unitService = $unitService;
        $this->baseUnitService = $baseUnitService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = $this->unitService->getAllUnits();
        return view('unit.index', [
            'units' => $units, // Placeholder for units
            'createRoute' => route('unit.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $baseUnits = $this->baseUnitService->getAllBaseUnits();
        return view('unit.create', [
            'action' => route('unit.store'),
            'method' => 'POST',
            'unit' => null,
            'baseUnits' => $baseUnits,
            'cancelRoute' => route('unit.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:45',
            'conversion_unit' => 'required|numeric|min:0',
            'to_base_unit_id' => 'required|exists:base_units,id',
        ]);

        $this->unitService->createUnit($validatedData);

        return redirect()->route('unit.index')->with('success', 'Unit created successfully.');
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
    public function edit(Unit $unit)
    {
        $baseUnits = $this->baseUnitService->getAllBaseUnits();
        return view('unit.edit', [
            'action' => route('unit.update', $unit->id),
            'method' => 'PUT',
            'unit' => $unit,
            'baseUnits' => $baseUnits,
            'cancelRoute' => route('unit.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:45',
            'conversion_unit' => 'required|numeric|min:0',
            'to_base_unit_id' => 'required|exists:base_units,id',
        ]);

        $this->unitService->updateUnit($unit, $validatedData);

        return redirect()->route('unit.index')->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $this->unitService->deleteUnit($unit);

        return redirect()->route('unit.index')->with('success', 'Unit deleted successfully.');
    }
}
