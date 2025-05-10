<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::all();
        return view('department.index', [
            'departments' => $departments, // Placeholder for categories
            'createRoute' => route('department.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('department.create', [
            'action' => route('department.store'),
            'method' => 'POST',
            'department' => null,
            'cancelRoute' => route('department.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        // Create the department
        $department = Department::create([
            'name' => $validatedData['name'],
        ]);

        // Redirect back to the department index with a success message
        return redirect()->route('department.index')->with('success', 'Department created successfully.');
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
    public function edit(Department $department)
    {
        return view('department.edit', [
            'action' => route('department.update', $department->id),
            'method' => 'PUT',
            'department' => $department,
            'cancelRoute' => route('department.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        // Update the department
        $department->update([
            'name' => $validatedData['name'],
        ]);

        // Redirect back to the department index with a success message
        return redirect()->route('department.index')->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        // Perform soft delete
        $department->delete();

        // Redirect back to the department index with a success message
        return redirect()->route('department.index')->with('success', 'Department deleted successfully.');
    }
}
