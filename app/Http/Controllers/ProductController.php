<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Services\OutletService;
use App\Services\CategoryService;
use App\Services\UnitService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;
    protected $outletService;
    protected $categoryService;
    protected $unitService;

    /**
     * Constructor to inject the services.
     */
    public function __construct(
        ProductService $productService,
        OutletService $outletService,
        CategoryService $categoryService,
        UnitService $unitService
    ) {
        $this->middleware('permission:product.view|product.*')->only(['index', 'show']);
        $this->middleware('permission:product.create|product.*')->only(['create', 'store']);
        $this->middleware('permission:product.edit|product.*')->only(['edit', 'update']);
        $this->middleware('permission:product.delete|product.*')->only(['destroy']);

        $this->productService = $productService;
        $this->outletService = $outletService;
        $this->categoryService = $categoryService;
        $this->unitService = $unitService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->productService->getAllProducts();
        return view('product.index', [
            'products' => $products,
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
        // Validate the incoming request
        $validatedData = $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'base_price' => 'nullable|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'min_qty' => 'required|numeric|min:0',
            'is_shown' => 'required|boolean',
            'categories_id' => 'required|exists:categories,id',
            'units_id' => 'required|exists:units,id',
            'outlets' => 'nullable|array',
            'outlets.*' => 'exists:outlets,id',
        ]);

        // Use the service to create the product
        $this->productService->createProduct($validatedData);

        // Redirect back to the product index with a success message
        return redirect()->route('product.index')->with('success', 'Product created successfully.');
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
        // Validate the incoming request
        $validatedData = $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'base_price' => 'nullable|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'min_qty' => 'required|numeric|min:0',
            'is_shown' => 'required|boolean',
            'categories_id' => 'required|exists:categories,id',
            'units_id' => 'required|exists:units,id',
            'outlets' => 'nullable|array',
            'outlets.*' => 'exists:outlets,id',
        ]);

        // Use the service to update the product
        $this->productService->updateProduct($product, $validatedData);

        // Redirect back to the product index with a success message
        return redirect()->route('product.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Use the service to delete the product
        $this->productService->deleteProduct($product);

        // Redirect back to the product index with a success message
        return redirect()->route('product.index')->with('success', 'Product deleted successfully.');
    }
}