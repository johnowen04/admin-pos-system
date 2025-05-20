<?php

namespace App\Http\Controllers;

use App\Services\FeatureService;
use App\Services\OperationService;
use App\Services\PermissionService;
use App\Services\PositionService;
use Illuminate\Http\Request;

class ACLController extends Controller
{
    protected $featureService;
    protected $operationService;
    protected $permissionService;
    protected $positionService;

    /**
     * Constructor to inject services.
     */
    public function __construct(
        FeatureService $featureService,
        OperationService $operationService,
        PermissionService $permissionService,
        PositionService $positionService
    ) {
        $this->middleware('permission:acl.view|acl.*')->only(['index']);
        $this->middleware('permission:acl.edit|acl.*')->only(['update']);

        $this->featureService = $featureService;
        $this->operationService = $operationService;
        $this->permissionService = $permissionService;
        $this->positionService = $positionService;
    }

    /**
     * Display the ACL matrix.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $positions = $this->positionService->getAllPositions();

        $permissions = $this->permissionService->getAllPermissions();

        [$aclMatrix, $featuresForTemplate] = $this->buildACLMatrix($positions, $permissions);

        return view('acl.index', [
            'action' => route('acl.update', ['acl' => 1]),
            'permissions' => $aclMatrix,
            'features' => $featuresForTemplate,
            'positions' => $positions->pluck('name')->toArray(),
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
            $updatedPositions = $this->positionService->updateACLMatrix(
                $permissions,
                $this->featureService,
                $this->operationService,
                $this->permissionService
            );

            $message = count($updatedPositions) . ' positions updated successfully.';
            return redirect()->route('acl.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('acl.index')->with('error', 'Failed to update permissions: ' . $e->getMessage());
        }
    }

    /**
     * Build the ACL matrix.
     *
     * @param \Illuminate\Database\Eloquent\Collection $positions
     * @param \Illuminate\Database\Eloquent\Collection $permissions
     * @return array
     */
    private function buildACLMatrix($positions, $permissions)
    {
        $features = $this->featureService->getAllFeatures();
        $operations = $this->operationService->getAllOperations();

        $aclMatrix = [];
        $featuresForTemplate = [];

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

                $permission = $permissions->first(function ($p) use ($feature, $operation) {
                    return $p->feature_id == $feature->id && $p->operation_id == $operation->id;
                });

                if (!$permission) continue;

                $positionPermissions = [];
                foreach ($positions as $position) {
                    $hasPermission = $position->permissions->contains('id', $permission->id) ? '1' : '0';
                    $positionPermissions[$position->name] = $hasPermission;

                    if (!isset($aclMatrix[$featureName])) {
                        $aclMatrix[$featureName] = [];
                    }
                    if (!isset($aclMatrix[$featureName][$operation->name])) {
                        $aclMatrix[$featureName][$operation->name] = [];
                    }
                    $aclMatrix[$featureName][$operation->name][$position->name] = $hasPermission;
                }

                $featureOperations[] = [
                    'id' => $operation->id,
                    'name' => $operation->name,
                    'permission_id' => $permission->id,
                    'slug' => $permission->slug,
                    'positions' => $positionPermissions
                ];

                $featureOperationNames[] = $operation->name;
            }

            if (empty($featureOperations)) continue;

            $featuresForTemplate[$featureName] = $featureOperationNames ?? [];

            unset($featureOperationNames);
        }

        return [$aclMatrix, $featuresForTemplate];
    }
}
