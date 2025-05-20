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
    protected $featureService;
    protected $operationService;
    protected $permissionService;

    /**
     * Constructor to inject the PermissionService.
     */
    public function __construct(FeatureService $featureService, OperationService $operationService, PermissionService $permissionService)
    {
        $this->middleware('permission:permission.view')->only(['index', 'show']);
        $this->middleware('permission:permission.create')->only(['create', 'store']);
        $this->middleware('permission:permission.edit')->only(['edit', 'update']);
        $this->middleware('permission:permission.delete')->only(['destroy']);

        $this->featureService = $featureService;
        $this->operationService = $operationService;
        $this->permissionService = $permissionService;
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
        // Validate the incoming request
        $validatedData = $request->validate([
            'feature_id' => 'required|exists:features,id', // Ensure the feature exists
            'operation_id' => 'required|exists:operations,id', // Ensure the operation exists
            'slug' => [
                'required',
                'string',
                'max:20',
                Rule::unique('permissions')->withoutTrashed() // Only check non-trashed records
            ],
        ]);

        // Use the service to create the permission
        $this->permissionService->createPermission($validatedData);

        // Redirect back to the permission index with a success message
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
        // Validate the incoming request
        $validatedData = $request->validate([
            'feature_id' => 'required|exists:features,id',
            'operation_id' => 'required|exists:operations,id',
            'slug' => [
                'required',
                'string',
                'max:20',
                Rule::unique('permissions')->ignore($permission->id)->withoutTrashed()
            ],
        ]);

        // Use the service to update the permission
        $this->permissionService->updatePermission($permission, $validatedData);

        // Redirect back to the permission index with a success message
        return redirect()->route('permission.index')->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // Use the service to delete the permission
        $this->permissionService->deletePermission($permission);

        // Redirect back to the permission index with a success message
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

        // Check if there is data in the arrays
        if (!empty($validatedData['feature_id']) || !empty($validatedData['operations'])) {
            // Prepare the data for batch creation
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
                        'slug' => $featureName . '.' . $operationName, // Example slug generation
                    ];
                }
            }
            // Use the service to create the permission
            $this->permissionService->createPermissionBatch($permissions);
        } else {
            // Handle the case where both arrays are empty
            return redirect()->back()->withErrors(['error' => 'At least one feature or operation must be selected.']);
        }

        // Redirect back to the permission index with a success message
        return redirect()->route('permission.index')->with('success', 'Permission created successfully.');
    }
}
