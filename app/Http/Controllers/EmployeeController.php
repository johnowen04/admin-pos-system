<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\OutletService;
use App\Services\RoleService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $roleService;
    protected $outletService;
    protected $employeeService;

    /**
     * Constructor to inject the EmployeeService.
     */
    public function __construct(RoleService $roleService, OutletService $outletService, EmployeeService $employeeService)
    {
        $this->roleService = $roleService;
        $this->outletService = $outletService;
        $this->employeeService = $employeeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = $this->employeeService->getAllEmployees();
        return view('employee.index', [
            'employees' => $employees,
            'createRoute' => route('employee.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = $this->outletService->getAllOutlets(); // Fetch all outlets
        $roles = $this->roleService->getAllRoles(); // Fetch all roles
        return view('employee.create', [
            'action' => route('employee.store'),
            'method' => 'POST',
            'employee' => null,
            'outlets' => $outlets,
            'selectedOutlets' => [], // No pre-selected outlets for create
            'roles' => $roles,
            'cancelRoute' => route('employee.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'nip' => 'required|string|max:50|unique:employees,nip',
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:users,email',
            'roles_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:8', // Password is required for new employees
            'outlets' => 'nullable|array', // Outlets can be null or an array
            'outlets.*' => 'exists:outlets,id', // Ensure each outlet exists
        ]);

        // Use the service to create the employee
        $this->employeeService->createEmployee($validatedData);

        // Redirect back with a success message
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
        $outlets = $this->outletService->getAllOutlets(); // Fetch all outlets
        $selectedOutlets = $this->employeeService->getSelectedOutlets($employee); // Get selected outlets for the employee
        $roles = $this->roleService->getAllRoles(); // Fetch all roles
        return view('employee.edit', [
            'action' => route('employee.update', $employee->id),
            'method' => 'PUT',
            'employee' => $employee,
            'outlets' => $outlets,
            'selectedOutlets' => $selectedOutlets, // No pre-selected outlets for create
            'roles' => $roles,
            'cancelRoute' => route('employee.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'nip' => 'required|string|max:50|unique:employees,nip,' . $employee->id,
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:users,email,' . $employee->user->id,
            'roles_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8', // Password is optional for updates
            'outlets' => 'nullable|array', // Outlets can be null or an array
            'outlets.*' => 'exists:outlets,id', // Ensure each outlet exists
        ]);

        // Use the service to update the employee
        $this->employeeService->updateEmployee($employee, $validatedData);

        // Redirect back with a success message
        return redirect()->route('employee.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        // Use the service to delete the employee
        $this->employeeService->deleteEmployee($employee);

        // Redirect back with a success message
        return redirect()->route('employee.index')->with('success', 'Employee deleted successfully.');
    }
}
