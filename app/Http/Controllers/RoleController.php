<?php

namespace App\Http\Controllers;

use App\Enums\RoleLevel;
use App\Models\Role;
use App\Services\PermissionService;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    protected $permissionService;
    protected $permissionRoleService;
    protected $roleService;

    /**
     * Constructor to inject the RoleService.
     */
    public function __construct(PermissionService $permissionService, RoleService $roleService)
    {
        $this->middleware('permission:role.view')->only(['index', 'show']);
        $this->middleware('permission:role.create')->only(['create', 'store']);
        $this->middleware('permission:role.edit')->only(['edit', 'update']);
        $this->middleware('permission:role.delete')->only(['destroy']);

        $this->permissionService = $permissionService;
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUserLevel = Auth::user()->employee->role->level->value;
        $roles = $this->roleService->getAllRoleWithLowerOrEqualLevel($currentUserLevel);
        
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
        $permissions = $this->permissionService->getAllPermissions();
        $currentUserLevel = Auth::user()->employee->role->level->value;

        $roleLevels = array_filter(RoleLevel::cases(), fn($level) => $level->value <= $currentUserLevel);

        // Group permissions by feature for easier handling in the view
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            return $permission->feature->id;
        })->map(function ($featurePermissions) {
            $firstPermission = $featurePermissions->first();
            return [
                'id' => $firstPermission->feature->id,
                'name' => $firstPermission->feature->name,
                'operations' => $featurePermissions->map(function ($permission) {
                    return [
                        'id' => $permission->operation->id,
                        'name' => $permission->operation->name,
                        'permission_id' => $permission->id,
                        'slug' => $permission->slug
                    ];
                })->values()->all()
            ];
        })->values()->all();

        return view('role.create', [
            'action' => route('role.store'),
            'method' => 'POST',
            'role' => null,
            'roleLevels' => $roleLevels,
            'permissions' => $groupedPermissions,
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
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('roles')->withoutTrashed() // Only check non-trashed records
            ],
            'level' => ['required', Rule::in(array_column(RoleLevel::cases(), 'value'))],
            'permissions' => 'nullable|array',
        ]);

        // Extract role data
        $roleData = [
            'name' => $validatedData['name'],
            'level' => $validatedData['level'],
        ];

        // Get permissions from request
        $permissionIds = [];
        if (isset($request->permissions)) {
            $permissionIds = array_keys($request->permissions);
        }

        // Use the service to create the role
        $this->roleService->createRole($roleData, $permissionIds);

        // Redirect back to the role index with a success message
        return redirect()->route('role.index')->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = $this->permissionService->getAllPermissions();
        $currentUserLevel = Auth::user()->employee->role->level->value;

        $roleLevels = array_filter(RoleLevel::cases(), fn($level) => $level->value <= $currentUserLevel);

        // Group permissions by feature for easier handling in the view
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            return $permission->feature->id;
        })->map(function ($featurePermissions) {
            $firstPermission = $featurePermissions->first();
            return [
                'id' => $firstPermission->feature->id,
                'name' => $firstPermission->feature->name,
                'operations' => $featurePermissions->map(function ($permission) {
                    return [
                        'id' => $permission->operation->id,
                        'name' => $permission->operation->name,
                        'permission_id' => $permission->id,
                        'slug' => $permission->slug
                    ];
                })->values()->all()
            ];
        })->values()->all();

        // Get current permissions for this role in the format expected by the blade template
        $currentPermissions = [];
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        foreach ($permissions as $permission) {
            if (in_array($permission->id, $rolePermissionIds)) {
                if (!isset($currentPermissions[$permission->feature->id])) {
                    $currentPermissions[$permission->feature->id] = [];
                }
                $currentPermissions[$permission->feature->id][$permission->operation->id] = true;
            }
        }

        return view('role.edit', [
            'action' => route('role.update', $role->id),
            'method' => 'PUT',
            'role' => $role,
            'roleLevels' => $roleLevels,
            'permissions' => $groupedPermissions,
            'currentPermissions' => $currentPermissions,
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
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('roles')->ignore($role->id)->withoutTrashed()
            ],
            'level' => ['required', Rule::in(array_column(RoleLevel::cases(), 'value'))],
            'permissions' => 'nullable|array',
        ]);

        // Get the current authenticated user's role level
        $currentUserLevel = Auth::user()->employee->role->level->value;

        // Authorization: user cannot assign equal or higher level than their own
        if ($validatedData['level'] > $currentUserLevel) {
            return back()->withErrors([
                'level' => 'You cannot assign a role level equal to or higher than your own.'
            ])->withInput();
        }

        // Extract role data
        $roleData = [
            'name' => $validatedData['name'],
            'level' => $validatedData['level'],
        ];

        // Get permissions from request
        $permissionIds = [];
        if (isset($request->permissions)) {
            $permissionIds = array_keys($request->permissions);
        }

        // Use the service to update the role and sync permissions
        $this->roleService->updateRole($role, $roleData, $permissionIds);

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
