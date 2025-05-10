<?php

namespace App\Http\Controllers;

use App\Models\BaseUnit;
use Illuminate\Http\Request;

class BaseUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $baseunits = BaseUnit::all();
        return view('baseunit.index', [
            'baseunits' => $baseunits, // Placeholder for base units
            'createRoute' => route('baseunit.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('baseunit.create', [
            'action' => route('baseunit.store'),
            'method' => 'POST',
            'baseunit' => null,
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
        BaseUnit::create($validatedData);

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
    public function edit(BaseUnit $baseunit)
    {
        return view('baseunit.edit', [
            'action' => route('baseunit.update', $baseunit->id),
            'method' => 'PUT',
            'baseunit' => $baseunit,
            'cancelRoute' => route('baseunit.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BaseUnit $baseunit)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:45',
        ]);

        // Update the base unit
        $baseunit->update($validatedData);

        // Redirect back to the base unit index with a success message
        return redirect()->route('baseunit.index')->with('success', 'Base Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BaseUnit $baseunit)
    {
        // Perform soft delete
        $baseunit->delete();

        // Redirect back to the base unit index with a success message
        return redirect()->route('baseunit.index')->with('success', 'Base Unit deleted successfully.');
    }
}
