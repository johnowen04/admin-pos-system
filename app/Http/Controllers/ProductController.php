<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Services\OutletService;
use App\Services\CategoryService;
use App\Services\UnitService;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Constructor to inject the services.
     */
    public function __construct(
        protected ProductService $productService,
        protected OutletService $outletService,
        protected CategoryService $categoryService,
        protected UnitService $unitService
    ) {
        $this->middleware('permission:product.view|product.*')->only(['index', 'show']);
        $this->middleware('permission:product.create|product.*')->only(['create', 'store']);
        $this->middleware('permission:product.edit|product.*')->only(['edit', 'update']);
        $this->middleware('permission:product.delete|product.*')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $selectedOutletId = session('selected_outlet_id');

        return view('product.index', [
            'selectedOutletId' => $selectedOutletId,
            'createRoute' => route('product.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = $this->outletService->getAllOutlets();
        $categories = $this->categoryService->getAllCategories();
        $units = $this->unitService->getAllUnits();
        return view('product.create', [
            'action' => route('product.store'),
            'method' => 'POST',
            'product' => null,
            'categories' => $categories,
            'units' => $units,
            'outlets' => $outlets,
            'selectedOutlets' => [],
            'cancelRoute' => route('product.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'buy_price' => 'nullable|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'min_qty' => 'required|numeric|min:0',
            'is_shown' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'outlets' => 'nullable|array',
            'outlets.*' => 'exists:outlets,id',
        ]);

        $this->productService->createProduct($validatedData);
        return redirect()->route('product.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $outlets = $this->outletService->getAllOutlets();
        $categories = $this->categoryService->getAllCategories();
        $units = $this->unitService->getAllUnits();
        $selectedOutlets = $this->productService->getSelectedOutlets($product);
        return view('product.edit', [
            'action' => route('product.update', $product),
            'method' => 'PUT',
            'product' => $product,
            'categories' => $categories,
            'units' => $units,
            'outlets' => $outlets,
            'selectedOutlets' => $selectedOutlets,
            'cancelRoute' => route('product.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'min_qty' => 'required|numeric|min:0',
            'is_shown' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'outlets' => 'nullable|array',
            'outlets.*' => 'exists:outlets,id',
        ]);

        $this->productService->updateProduct($product, $validatedData);
        return redirect()->route('product.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product);
        return redirect()->route('product.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Show the form for importing products.
     */
    public function importProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::queueImport(new ProductImport(), $request->file('file'));

        return back()->with('success', 'Products imported successfully.');
    }
}
