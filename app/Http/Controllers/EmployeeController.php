<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\AccessControlService;
use App\Services\EmployeeService;
use App\Services\OutletService;
use App\Services\PositionService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $positionService;
    protected $outletService;
    protected $employeeService;
    protected $accessControlService;

    /**
     * Constructor to inject the EmployeeService.
     */
    public function __construct(PositionService $positionService, OutletService $outletService, EmployeeService $employeeService)
    {
        $this->middleware('permission:employee.view|employee.*')->only(['index', 'show']);
        $this->middleware('permission:employee.create|employee.*')->only(['create', 'store']);
        $this->middleware('permission:employee.edit|employee.*')->only(['edit', 'update']);
        $this->middleware('permission:employee.delete|employee.*')->only(['destroy']);

        $this->positionService = $positionService;
        $this->outletService = $outletService;
        $this->employeeService = $employeeService;

        $this->accessControlService = app(AccessControlService::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUserLevel = $this->accessControlService->getCurrentUserLevel();
        $employees = $this->employeeService->getAllEmployeesWithLowerOrEqualPosition(positionLevel: $currentUserLevel, withTrashedPosition: true); 

        return view('employee.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = $this->outletService->getAllOutlets();
        $currentUserLevel = $this->accessControlService->getCurrentUserLevel();
        $positions = $this->positionService->getAllPositionWithLowerOrEqualLevel($currentUserLevel);
        return view('employee.create', [
            'action' => route('employee.store'),
            'method' => 'POST',
            'employee' => null,
            'outlets' => $outlets,
            'selectedOutlets' => [],
            'positions' => $positions,
            'cancelRoute' => route('employee.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nip' => 'required|string|max:50|unique:employees,nip',
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:users,email',
            'username' => 'nullable|string|max:100|unique:users,username,',
            'password' => 'nullable|string|min:8',
            'position_id' => 'required|exists:positions,id',
            'outlets' => 'nullable|array',
            'outlets.*' => 'exists:outlets,id',
        ]);

        $this->employeeService->createEmployee($validatedData);
        return redirect()->route('employee.index')->with('success', 'Employee created successfully.');
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
    public function edit(Employee $employee)
    {
        $outlets = $this->outletService->getAllOutlets();
        $selectedOutlets = $this->employeeService->getSelectedOutlets($employee);
        $currentUserLevel = $this->accessControlService->getCurrentUserLevel();
        $positions = $this->positionService->getAllPositionWithLowerOrEqualLevel($currentUserLevel);
        return view('employee.edit', [
            'action' => route('employee.update', $employee->id),
            'method' => 'PUT',
            'employee' => $employee,
            'outlets' => $outlets,
            'selectedOutlets' => $selectedOutlets,
            'positions' => $positions,
            'cancelRoute' => route('employee.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validatedData = $request->validate([
            'nip' => 'required|string|max:50|unique:employees,nip,' . $employee->id,
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:users,email,' . $employee->user->id,
            'username' => 'nullable|string|max:100|unique:users,username,' . $employee->user->id,
            'password' => 'nullable|string|min:8',
            'position_id' => 'required|exists:positions,id',
            'outlets' => 'nullable|array',
            'outlets.*' => 'exists:outlets,id',
        ]);

        $this->employeeService->updateEmployee($employee, $validatedData);

        return redirect()->route('employee.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $this->employeeService->deleteEmployee($employee);
        return redirect()->route('employee.index')->with('success', 'Employee deleted successfully.');
    }
}
