<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Constructor to inject the DepartmentService.
     */
    public function __construct(protected DepartmentService $departmentService)
    {
        $this->middleware('permission:department.view|department.*')->only(['index', 'show']);
        $this->middleware('permission:department.create|department.*')->only(['create', 'store']);
        $this->middleware('permission:department.edit|department.*')->only(['edit', 'update']);
        $this->middleware('permission:department.delete|department.*')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = $this->departmentService->getAllDepartments();
        return view('department.index', [
            'departments' => $departments,
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
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $this->departmentService->createDepartment($validatedData);
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
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $this->departmentService->updateDepartment($department, $validatedData);
        return redirect()->route('department.index')->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $this->departmentService->deleteDepartment($department);
        return redirect()->route('department.index')->with('success', 'Department deleted successfully.');
    }
}