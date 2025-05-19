<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Services\DepartmentService;
use App\Services\OutletService;

class CategoryController extends Controller
{
    protected $departmentService;
    protected $outletService;
    protected $categoryService;

    /**
     * Constructor to inject the CategoryService.
     */
    public function __construct(DepartmentService $departmentService, OutletService $outletService, CategoryService $categoryService)
    {
        $this->middleware('permission:category.view')->only(['index', 'show']);
        $this->middleware('permission:category.create')->only(['create', 'store']);
        $this->middleware('permission:category.edit')->only(['edit', 'update']);
        $this->middleware('permission:category.delete')->only(['destroy']);

        $this->departmentService = $departmentService;
        $this->outletService = $outletService;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        return view('category.index', [
            'categories' => $categories,
            'createRoute' => route('category.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = $this->departmentService->getAllDepartments(); // Fetch all departments
        $outlets = $this->outletService->getAllOutlets(); // Fetch all outlets
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
        $departments = $this->departmentService->getAllDepartments(); // Fetch all departments
        $outlets = $this->outletService->getAllOutlets(); // Fetch all outlets
        $selectedOutlets = $this->categoryService->getSelectedOutlets($category); // Get selected outlet IDs
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
            'name' => 'required|string|max:100',
            'departments_id' => 'required|exists:departments,id',
            'is_shown' => 'required|boolean',
            'outlets' => 'nullable|array',
            'outlets.*' => 'exists:outlets,id',
        ]);

        // Use the service to update the category
        $this->categoryService->updateCategory($category, $validatedData);

        // Redirect back to the category index with a success message
        return redirect()->route('category.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Use the service to delete the category
        $this->categoryService->deleteCategory($category);

        // Redirect back to the category index with a success message
        return redirect()->route('category.index')->with('success', 'Category deleted successfully.');
    }
}
