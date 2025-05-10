<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\Department;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('category.index', [
            'categories' => $categories, // Placeholder for categories
            'createRoute' => route('category.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = Outlet::all(); // Fetch all outlets
        $departments = Department::all(); // Fetch all outlets
        return view('category.create', [
            'action' => route('category.store'),
            'method' => 'POST',
            'category' => null,
            'outlets' => $outlets,
            'selectedOutlets' => [], // No pre-selected outlets for create
            'departments' => $departments,
            'cancelRoute' => route('category.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:45',
            'departments_id' => 'required|exists:departments,id', // Ensure the department exists
            'is_shown' => 'required|boolean',
            'outlets' => 'nullable|array', // Outlets can be null or an array
            'outlets.*' => 'exists:outlets,id', // Ensure each outlet exists
        ]);

        // Create the category
        $category = Category::create([
            'name' => $validatedData['name'],
            'departments_id' => $validatedData['departments_id'],
            'is_shown' => $validatedData['is_shown'],
        ]);

        // Attach outlets to the category (if any)
        if (!empty($validatedData['outlets'])) {
            $category->outlets()->sync($validatedData['outlets']);
        }

        // Redirect back to the category index with a success message
        return redirect()->route('category.index')->with('success', 'Category created successfully.');
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
    public function edit(Category $category)
    {
        $outlets = Outlet::all(); // Fetch all outlets
        $selectedOutlets = $category->outlets->pluck('id')->toArray(); // Get selected outlet IDs
        $departments = Department::all(); // Fetch all outlets
        return view('category.edit', [
            'action' => route('category.update', $category->id),
            'method' => 'PUT',
            'category' => $category,
            'outlets' => $outlets,
            'selectedOutlets' => $selectedOutlets, // Pre-selected outlets for the category
            'departments' => $departments,
            'cancelRoute' => route('category.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:45',
            'departments_id' => 'required|exists:departments,id', // Ensure the department exists
            'is_shown' => 'required|boolean',
            'outlets' => 'nullable|array', // Outlets can be null or an array
            'outlets.*' => 'exists:outlets,id', // Ensure each outlet exists
        ]);

        // Update the category
        $category->update([
            'name' => $validatedData['name'],
            'departments_id' => $validatedData['departments_id'],
            'is_shown' => $validatedData['is_shown'],
        ]);

        // Sync outlets with the category (if any)
        if (!empty($validatedData['outlets'])) {
            $category->outlets()->sync($validatedData['outlets']);
        } else {
            $category->outlets()->detach(); // Detach all outlets if none are provided
        }

        // Redirect back to the category index with a success message
        return redirect()->route('category.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Perform soft delete
        $category->delete();

        // Redirect back to the category index with a success message
        return redirect()->route('category.index')->with('success', 'Category deleted successfully.');
    }
}
