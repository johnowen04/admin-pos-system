<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Services\FeatureService;
use App\Services\OperationService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{

    /**
     * Constructor to inject the PermissionService.
     */
    public function __construct(
        protected FeatureService $featureService,
        protected OperationService $operationService,
        protected PermissionService $permissionService
    ) {
        $this->middleware('permission:permission.view|permission.*')->only(['index', 'show']);
        $this->middleware('permission:permission.create|permission.*')->only(['create', 'store']);
        $this->middleware('permission:permission.edit|permission.*')->only(['edit', 'update']);
        $this->middleware('permission:permission.delete|permission.*')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = $this->featureService->getAllFeatures();
        $operations = $this->operationService->getAllOperations();
        $permissions = $this->permissionService->getAllPermissions(withTrashedFeatures: true, withTrashedOperations: true);
        return view('permission.index', [
            'features' => $features,
            'operations' => $operations,
            'permissions' => $permissions,
            'createRoute' => route('permission.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $features = $this->featureService->getAllFeatures();
        $operations = $this->operationService->getAllOperations();
        return view('permission.create', [
            'action' => route('permission.store'),
            'method' => 'POST',
            'features' => $features,
            'operations' => $operations,
            'permission' => null,
            'cancelRoute' => route('permission.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'feature_id' => 'required|exists:features,id',
            'operation_id' => 'required|exists:operations,id',
            'slug' => [
                'required',
                'string',
                'max:20',
                Rule::unique('permissions')->withoutTrashed()
            ],
            'is_super_user_only' => 'boolean',
        ]);

        $this->permissionService->createPermission($validatedData);
        return redirect()->route('permission.index')->with('success', 'Permission created successfully.');
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
    public function edit(Permission $permission)
    {
        $features = $this->featureService->getAllFeatures();
        $operations = $this->operationService->getAllOperations();
        return view('permission.edit', [
            'action' => route('permission.update', $permission->id),
            'method' => 'PUT',
            'features' => $features,
            'operations' => $operations,
            'permission' => $permission,
            'cancelRoute' => route('permission.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validatedData = $request->validate([
            'feature_id' => 'required|exists:features,id',
            'operation_id' => 'required|exists:operations,id',
            'slug' => [
                'required',
                'string',
                'max:20',
                Rule::unique('permissions')->ignore($permission->id)->withoutTrashed()
            ],
            'is_super_user_only' => 'boolean',
        ]);

        $this->permissionService->updatePermission($permission, $validatedData);
        return redirect()->route('permission.index')->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $this->permissionService->deletePermission($permission);
        return redirect()->route('permission.index')->with('success', 'Permission deleted successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function batch(Request $request)
    {
        $validatedData = $request->validate([
            'feature_id' => 'required|exists:features,id',
            'operations' => 'required|array',
            'operations.*' => 'exists:operations,id',
        ]);

        if (!empty($validatedData['feature_id']) || !empty($validatedData['operations'])) {
            $permissions = [];
            if (!empty($validatedData['feature_id']) && !empty($validatedData['operations'])) {
                $featureId = $validatedData['feature_id'];

                foreach ($validatedData['operations'] as $_ => $operationId) {
                    $featureName = $this->featureService->getFeatureById($featureId)->name;
                    $operationName = $this->operationService->getOperationById($operationId)->name;

                    if (strpos($featureName, ' ') !== false) {
                        $words = explode(' ', $featureName);
                        $abbreviation = '';
                        foreach ($words as $word) {
                            if (isset($word[0])) {
                                $abbreviation .= $word[0];
                            }
                        }
                        $featureName = $abbreviation;
                    }

                    $featureName = strtolower($featureName);
                    $operationName = strtolower($operationName);

                    $permissions[] = [
                        'feature_id' => $featureId,
                        'operation_id' => $operationId,
                        'slug' => $featureName . '.' . $operationName,
                        'is_super_user_only' => false,
                    ];
                }
            }
            $this->permissionService->createPermissionBatch($permissions);
        } else {
            return redirect()->back()->withErrors(['error' => 'At least one feature or operation must be selected.']);
        }

        return redirect()->route('permission.index')->with('success', 'Permission created successfully.');
    }

    public function toggleSuperUserOnly(Request $request)
    {
        try {
            $request->validate([
                'permission_id' => 'required|exists:permissions,id',
                'is_super_user_only' => 'required|boolean',
            ]);

            $permission = Permission::findOrFail($request->permission_id);
            $oldValue = $permission->is_super_user_only;
            $permission->is_super_user_only = $request->is_super_user_only;
            $permission->save();

            $newStatus = $request->is_super_user_only ? 'enabled' : 'disabled';

            return response()->json([
                'success' => true,
                'message' => "Super User Only {$newStatus} for '{$permission->slug}'",
                'permission' => [
                    'id' => $permission->id,
                    'slug' => $permission->slug,
                    'is_super_user_only' => $permission->is_super_user_only
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission: ' . $e->getMessage()
            ], 500);
        }
    }
}
