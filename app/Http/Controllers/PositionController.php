<?php

namespace App\Http\Controllers;

use App\Enums\PositionLevel;
use App\Models\Position;
use App\Services\AccessControlService;
use App\Services\PermissionService;
use App\Services\PositionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    protected $accessControlService;

    /**
     * Constructor to inject the PositionService.
     */
    public function __construct(
        protected PermissionService $permissionService,
        protected PositionService $positionService)
    {
        $this->middleware('permission:position.view|position.*')->only(['index', 'show']);
        $this->middleware('permission:position.create|position.*')->only(['create', 'store']);
        $this->middleware('permission:position.edit|position.*')->only(['edit', 'update']);
        $this->middleware('permission:position.delete|position.*')->only(['destroy']);

        $this->accessControlService = app(AccessControlService::class);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $currentUserLevel = $this->accessControlService->getCurrentUserLevel();
        $positions = $this->positionService->getAllPositionWithLowerOrEqualLevel($currentUserLevel);
        return view('position.index', [
            'positions' => $positions,
            'createRoute' => route('position.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentUserLevel = $this->accessControlService->getCurrentUserLevel();

        if ($this->accessControlService->isSuperUser()) {
            $positionLevels = array_filter(PositionLevel::cases(), fn($level) => $currentUserLevel >= $level->value);
        } else {
            $positionLevels = array_filter(PositionLevel::cases(), fn($level) => $level->value <= $currentUserLevel);
        }

        $permissions = $this->permissionService->getAllPermissions(onlyNonSuperUser: true);

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

        return view('position.create', [
            'action' => route('position.store'),
            'method' => 'POST',
            'position' => null,
            'positionLevels' => $positionLevels,
            'permissions' => $groupedPermissions,
            'cancelRoute' => route('position.index'),
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
                Rule::unique('positions')->withoutTrashed()
            ],
            'level' => ['required', Rule::in(array_column(PositionLevel::cases(), 'value'))],
            'permissions' => 'nullable|array',
        ]);

        $positionData = [
            'name' => $validatedData['name'],
            'level' => $validatedData['level'],
        ];

        $permissionIds = [];
        if (isset($request->permissions)) {
            $permissionIds = array_keys($request->permissions);
        }

        $this->positionService->createPosition($positionData, $permissionIds);
        return redirect()->route('position.index')->with('success', 'Position created successfully.');
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
    public function edit(Position $position)
    {
        $currentUserLevel = $this->accessControlService->getCurrentUserLevel();

        if ($this->accessControlService->isSuperUser()) {
            $positionLevels = array_filter(PositionLevel::cases(), fn($level) => $currentUserLevel >= $level->value);
        } else {
            $positionLevels = array_filter(PositionLevel::cases(), fn($level) => $level->value <= $currentUserLevel);
        }
        
        $permissions = $this->permissionService->getAllPermissions(onlyNonSuperUser: true);

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

        $currentPermissions = [];
        $positionPermissionIds = $position->permissions->pluck('id')->toArray();

        foreach ($permissions as $permission) {
            if (in_array($permission->id, $positionPermissionIds)) {
                if (!isset($currentPermissions[$permission->feature->id])) {
                    $currentPermissions[$permission->feature->id] = [];
                }
                $currentPermissions[$permission->feature->id][$permission->operation->id] = true;
            }
        }

        return view('position.edit', [
            'action' => route('position.update', $position->id),
            'method' => 'PUT',
            'position' => $position,
            'positionLevels' => $positionLevels,
            'permissions' => $groupedPermissions,
            'currentPermissions' => $currentPermissions,
            'cancelRoute' => route('position.index'),
        ]);
    }

    /**
     * Update the specified position in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Position $position
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Position $position)
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('positions')->ignore($position->id)->withoutTrashed()
            ],
            'level' => ['required', Rule::in(array_column(PositionLevel::cases(), 'value'))],
            'permissions' => 'nullable|array',
        ]);

        $currentUserLevel = $this->accessControlService->getCurrentUserLevel();

        if ($validatedData['level'] > $currentUserLevel) {
            return back()->withErrors([
                'level' => 'You cannot assign a position level equal to or higher than your own.'
            ])->withInput();
        }

        $positionData = [
            'name' => $validatedData['name'],
            'level' => $validatedData['level'],
        ];

        $permissionIds = [];
        if (isset($request->permissions)) {
            $permissionIds = array_keys($request->permissions);
        }

        $this->positionService->updatePosition($position, $positionData, $permissionIds);
        return redirect()->route('position.index')->with('success', 'Position updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        $this->positionService->deletePosition($position);
        return redirect()->route('position.index')->with('success', 'Position deleted successfully.');
    }
}
