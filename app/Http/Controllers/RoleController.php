<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return view('role.index', [
            'roles' => $roles, // Placeholder for roles
            'createRoute' => route('role.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('role.create', [
            'action' => route('role.store'),
            'method' => 'POST',
            'role' => null,
            'cancelRoute' => route('role.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:20|unique:roles,name',
        ]);

        // Create the outlet
        Role::create($validatedData);

        // Redirect back to the role index with a success message
        return redirect()->route('role.index')->with('success', 'Role created successfully.');
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
    public function edit(Role $role)
    {
        return view('role.edit', [
            'action' => route('role.update', $role->id),
            'method' => 'PUT',
            'role' => $role,
            'cancelRoute' => route('role.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:20|unique:roles,name',
        ]);

        // Update the role
        $role->update($validatedData);

        // Redirect back to the role index with a success message
        return redirect()->route('role.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Perform soft delete
        $role->delete();

        // Redirect back to the role index with a success message
        return redirect()->route('role.index')->with('success', 'Role deleted successfully.');
    }
}
