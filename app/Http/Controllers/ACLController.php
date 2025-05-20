<?php

namespace App\Http\Controllers;

use App\Services\FeatureService;
use App\Services\OperationService;
use App\Services\PermissionService;
use App\Services\RoleService;
use Illuminate\Http\Request;

class ACLController extends Controller
{
    protected $featureService;
    protected $operationService;
    protected $permissionService;
    protected $roleService;

    /**
     * Constructor to inject services.
     */
    public function __construct(
        FeatureService $featureService,
        OperationService $operationService,
        PermissionService $permissionService,
        RoleService $roleService
    ) {
        $this->middleware('permission:acl.view')->only(['index']);
        $this->middleware('permission:acl.edit')->only(['update']);

        $this->featureService = $featureService;
        $this->operationService = $operationService;
        $this->permissionService = $permissionService;
        $this->roleService = $roleService;
    }

    /**
     * Display the ACL matrix.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get all active roles
        $roles = $this->roleService->getAllRoles();

        // Get all permissions with their relationships
        $permissions = $this->permissionService->getAllPermissions();

        // Build ACL matrix
        [$aclMatrix, $featuresForTemplate] = $this->buildACLMatrix($roles, $permissions);

        return view('acl.index', [
            'action' => route('acl.update', ['acl' => 1]),
            'permissions' => $aclMatrix,
            'features' => $featuresForTemplate,
            'roles' => $roles->pluck('name')->toArray(),
            'method' => 'PUT'
        ]);
    }

    /**
     * Update the permissions matrix.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $permissions = $request->input('permissions', []);

        try {
            // The service now handles the transaction
            $updatedRoles = $this->roleService->updateACLMatrix(
                $permissions,
                $this->featureService,
                $this->operationService,
                $this->permissionService
            );

            $message = count($updatedRoles) . ' roles updated successfully.';
            return redirect()->route('acl.index')->with('success', $message);
        } catch (\Exception $e) {
            // Just handle the exception message here
            return redirect()->route('acl.index')->with('error', 'Failed to update permissions: ' . $e->getMessage());
        }
    }

    /**
     * Build the ACL matrix.
     *
     * @param \Illuminate\Database\Eloquent\Collection $roles
     * @param \Illuminate\Database\Eloquent\Collection $permissions
     * @return array
     */
    private function buildACLMatrix($roles, $permissions)
    {
        $features = $this->featureService->getAllFeatures();
        $operations = $this->operationService->getAllOperations();

        // Create matrix in the expected format
        $aclMatrix = [];
        $featuresForTemplate = [];

        // Group permissions by feature
        $permissionsByFeature = $permissions->groupBy('feature_id');

        foreach ($features as $feature) {
            if (!$feature->name || !isset($permissionsByFeature[$feature->id])) {
                continue;
            }

            $featureName = $feature->name;
            $featureOperations = [];

            // Process operations for this feature
            foreach ($operations as $operation) {
                if (!$operation->name) continue;

                // Find permission for this feature-operation combination
                $permission = $permissions->first(function ($p) use ($feature, $operation) {
                    return $p->feature_id == $feature->id && $p->operation_id == $operation->id;
                });

                if (!$permission) continue;

                // Create role permissions mapping
                $rolePermissions = [];
                foreach ($roles as $role) {
                    $hasPermission = $role->permissions->contains('id', $permission->id) ? '1' : '0';
                    $rolePermissions[$role->name] = $hasPermission;

                    // Add to feature matrix for the blade template
                    if (!isset($aclMatrix[$featureName])) {
                        $aclMatrix[$featureName] = [];
                    }
                    if (!isset($aclMatrix[$featureName][$operation->name])) {
                        $aclMatrix[$featureName][$operation->name] = [];
                    }
                    $aclMatrix[$featureName][$operation->name][$role->name] = $hasPermission;
                }

                // Add this operation to the feature's operations list
                $featureOperations[] = [
                    'id' => $operation->id,
                    'name' => $operation->name,
                    'permission_id' => $permission->id,
                    'slug' => $permission->slug,
                    'roles' => $rolePermissions
                ];

                // Track operations for template
                $featureOperationNames[] = $operation->name;
            }

            // Skip features with no operations
            if (empty($featureOperations)) continue;

            // Store operations for template
            $featuresForTemplate[$featureName] = $featureOperationNames ?? [];

            // Clear for next iteration
            unset($featureOperationNames);
        }

        // Return both the ACL matrix and the features structure
        return [$aclMatrix, $featuresForTemplate];
    }
}
