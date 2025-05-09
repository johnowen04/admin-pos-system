<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Outlet;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::all();
        return view('employee.index', [
            'employees' => $employees, // Placeholder for employees
            'createRoute' => route('employee.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = Outlet::all(); // Fetch all outlets
        $roles = Role::all(); // Fetch all outlets
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

        // Create the employee
        $employee = Employee::create([
            'nip' => $validatedData['nip'],
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'roles_id' => $validatedData['roles_id'],
        ]);

        // Create the associated user
        $employee->user()->create([
            'name' => $validatedData['name'], // Use the employee's name for the user
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // Hash the password
            'nip' => $employee->nip, // Link the user to the employee
        ]);

        // Sync outlets
        if (!empty($validatedData['outlets'])) {
            $employee->outlets()->sync($validatedData['outlets']);
        }

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
        $outlets = Outlet::all(); // Fetch all outlets
        $selectedOutlets = $employee->outlets->pluck('id')->toArray(); // Get selected outlets
        $roles = Role::all(); // Fetch all outlets
        return view('employee.edit', [
            'action' => route('employee.update', $employee->nip),
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
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:users,email,' . $employee->user->id, // Ensure unique email for the user
            'roles_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8', // Password is optional for updates
            'outlets' => 'nullable|array', // Outlets can be null or an array
            'outlets.*' => 'exists:outlets,id', // Ensure each outlet exists
        ]);

        // Update employee details
        $employee->update([
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'roles_id' => $validatedData['roles_id'],
        ]);

        // Update the associated user
        $userData = [
            'email' => $validatedData['email'],
        ];

        // Only update the password if it is provided
        if (!empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        $employee->user->update($userData);

        // Sync outlets
        if (!empty($validatedData['outlets'])) {
            $employee->outlets()->sync($validatedData['outlets']);
        } else {
            $employee->outlets()->detach(); // Detach all outlets if none are provided
        }

        // Redirect back with a success message
        return redirect()->route('employee.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
