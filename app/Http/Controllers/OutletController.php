<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $outlets = Outlet::all();
        return view('outlet.index', [
            'outlets' => $outlets, // Placeholder for outlets
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

        // Create the outlet
        Outlet::create($validatedData);

        // Redirect back to the outlet index with a success message
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

        // Update the outlet
        $outlet->update($validatedData);

        // Redirect back to the outlet index with a success message
        return redirect()->route('outlet.index')->with('success', 'Outlet updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Outlet $outlet)
    {
        // Perform soft delete
        $outlet->delete();

        // Redirect back to the department index with a success message
        return redirect()->route('outlet.index')->with('success', 'Outlet deleted successfully.');
    }
}
