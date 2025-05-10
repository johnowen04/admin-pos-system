<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\BaseUnit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::all();
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
        $baseunits = BaseUnit::all();
        return view('unit.create', [
            'action' => route('unit.store'),
            'method' => 'POST',
            'unit' => null,
            'baseunits' => $baseunits,
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

        Unit::create($validatedData);

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
        $baseunits = BaseUnit::all();
        return view('unit.edit', [
            'action' => route('unit.update', $unit->id),
            'method' => 'PUT',
            'unit' => $unit,
            'baseunits' => $baseunits,
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

        $unit->update($validatedData);

        return redirect()->route('unit.index')->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('unit.index')->with('success', 'Unit deleted successfully.');
    }
}
