<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;

    /**
     * Constructor to inject the RoleService.
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = $this->roleService->getAllRoles();
        return view('role.index', [
            'roles' => $roles,
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

        // Use the service to create the role
        $this->roleService->createRole($validatedData);

        // Redirect back to the role index with a success message
        return redirect()->route('role.index')->with('success', 'Role created successfully.');
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
            'name' => 'required|string|max:20|unique:roles,name,' . $role->id,
        ]);

        // Use the service to update the role
        $this->roleService->updateRole($role, $validatedData);

        // Redirect back to the role index with a success message
        return redirect()->route('role.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Use the service to delete the role
        $this->roleService->deleteRole($role);

        // Redirect back to the role index with a success message
        return redirect()->route('role.index')->with('success', 'Role deleted successfully.');
    }
}