<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Services\DepartmentService;
use App\Services\OutletService;

class CategoryController extends Controller
{
    /**
     * Constructor to inject the CategoryService.
     */
    public function __construct(
        protected DepartmentService $departmentService,
        protected OutletService $outletService,
        protected CategoryService $categoryService)
    {
        $this->middleware('permission:category.view|category.*')->only(['index', 'show']);
        $this->middleware('permission:category.create|category.*')->only(['create', 'store']);
        $this->middleware('permission:category.edit|category.*')->only(['edit', 'update']);
        $this->middleware('permission:category.delete|category.*')->only(['destroy']);
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
        $departments = $this->departmentService->getAllDepartments();
        $outlets = $this->outletService->getAllOutlets();
        return view('category.create', [
            'action' => route('category.store'),
            'method' => 'POST',
            'category' => null,
            'outlets' => $outlets,
            'selectedOutlets' => [],
            'departments' => $departments,
            'cancelRoute' => route('category.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:45',
            'departments_id' => 'required|exists:departments,id',
            'is_shown' => 'required|boolean',
            'outlets' => 'nullable|array',
            'outlets.*' => 'exists:outlets,id',
        ]);

        $this->categoryService->createCategory($validatedData);
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
        $departments = $this->departmentService->getAllDepartments();
        $outlets = $this->outletService->getAllOutlets();
        $selectedOutlets = $this->categoryService->getSelectedOutlets($category);
        return view('category.edit', [
            'action' => route('category.update', $category->id),
            'method' => 'PUT',
            'category' => $category,
            'outlets' => $outlets,
            'selectedOutlets' => $selectedOutlets,
            'departments' => $departments,
            'cancelRoute' => route('category.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'departments_id' => 'required|exists:departments,id',
            'is_shown' => 'required|boolean',
            'outlets' => 'nullable|array',
            'outlets.*' => 'exists:outlets,id',
        ]);

        $this->categoryService->updateCategory($category, $validatedData);
        return redirect()->route('category.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->categoryService->deleteCategory($category);
        return redirect()->route('category.index')->with('success', 'Category deleted successfully.');
    }
}
