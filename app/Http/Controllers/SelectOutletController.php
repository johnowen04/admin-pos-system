<?php

namespace App\Http\Controllers;

use App\Services\AccessControlService;
use App\Services\OutletService;
use Illuminate\Http\Request;

class SelectOutletController extends Controller
{
    public function __construct(
        protected OutletService $outletService,
        protected AccessControlService $accessControlService
    ) {
        //
    }

    /**
     * Controller to select an outlet.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        $id = $request->input('id');

        if ($id === 'all') {
            $this->setSelectedOutletSession('All Outlet', 'all');

            // Check if it's an AJAX request
            if ($request->ajax()) {
                return response()->json(['status' => 'ok']);
            }

            // For regular form submit, redirect to POS
            return redirect()->route('pos.index');
        }

        $outlet = $this->outletService->getOutletById($id);

        if (!$this->canAccessOutlet($outlet)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Invalid outlet'], 403);
            }

            return redirect()->route('pos.index')
                ->with('error', 'You do not have access to the selected outlet.');
        }

        $this->setSelectedOutletSession($outlet->name, $outlet->id);

        // Check if it's an AJAX request
        if ($request->ajax()) {
            return response()->json(['status' => 'ok']);
        }

        // For regular form submit, redirect to POS
        return redirect()->route('pos.index');
    }

    /**
     * Get the currently selected outlet name.
     *
     * @return string
     */
    private function setSelectedOutletSession(string $name, $id): void
    {
        session([
            'selected_outlet' => $name,
            'selected_outlet_id' => $id,
        ]);
    }

    /**
     * Check if the user can access the specified outlet.
     *
     * @param mixed $outlet
     * @return bool
     */
    private function canAccessOutlet($outlet): bool
    {
        if ($this->accessControlService->isSuperUser()) {
            return true;
        }

        $user = $this->accessControlService->getUser();

        return $outlet && $user->employee->outlets->contains($outlet);
    }
}
