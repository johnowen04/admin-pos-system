<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Constructor to inject the RoleService.
     */
    public function __construct(protected RoleService $roleService)
    {
        $this->middleware('permission:role.view|role.*')->only(['index', 'show']);
        $this->middleware('permission:role.create|role.*')->only(['create', 'store']);
        $this->middleware('permission:role.edit|role.*')->only(['edit', 'update']);
        $this->middleware('permission:role.delete|role.*')->only(['destroy']);
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
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('roles')->withoutTrashed()
            ],
        ]);

        $this->roleService->createRole($validatedData);

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
     * Update the specified role in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('roles')->ignore($role->id)->withoutTrashed()
            ],
        ]);

        $this->roleService->updateRole($role, $validatedData);

        return redirect()->route('role.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $this->roleService->deleteRole($role);

        return redirect()->route('role.index')->with('success', 'Role deleted successfully.');
    }
}
