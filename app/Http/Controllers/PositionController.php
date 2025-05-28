<?php

namespace App\Http\Controllers;

use App\Enums\PositionLevel;
use App\Models\Position;
use App\Services\AccessControlService;
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

        return view('position.create', [
            'action' => route('position.store'),
            'method' => 'POST',
            'position' => null,
            'positionLevels' => $positionLevels,
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
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $this->positionService->createPosition($validatedData);
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

        return view('position.edit', [
            'action' => route('position.update', $position->id),
            'method' => 'PUT',
            'position' => $position,
            'positionLevels' => $positionLevels,
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
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $currentUserLevel = $this->accessControlService->getCurrentUserLevel();

        if ($validatedData['level'] > $currentUserLevel) {
            return back()->withErrors([
                'level' => 'You cannot assign a position level equal to or higher than your own.'
            ])->withInput();
        }

        $this->positionService->updatePosition($position, $validatedData);
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
