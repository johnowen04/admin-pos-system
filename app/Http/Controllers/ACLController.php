<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Services\PositionService;
use Illuminate\Http\Request;

class ACLController extends Controller
{
    /**
     * Constructor to inject services.
     */
    public function __construct(
        protected PositionService $positionService
    ) {
        $this->middleware('permission:acl.view|acl.*')->only(['index']);
        $this->middleware('permission:acl.edit|acl.*')->only(['update']);
    }

    /**
     * Display the ACL matrix.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('acl.index', [
            'action' => route('acl.update', ['acl' => 1]),
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
        $validatedData = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'array',
            'permissions.*.*' => 'nullable|exists:permissions,id',
        ]);

        $permissions = $validatedData['permissions'];

        foreach ($permissions as $positionId => $permissionIds) {
            $position = Position::find($positionId);
            $data['permissions'] = array_filter($permissionIds);
            $this->positionService->updatePosition($position, $data);
        }

        return redirect()->route('acl.index')->with('success', 'Positions updated successfully.');
    }
}
