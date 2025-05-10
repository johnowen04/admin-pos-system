<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Outlet;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return view('product.index', [
            'products' => $products, // Placeholder for product
            'createRoute' => route('product.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = Outlet::all(); // Fetch all outlets
        $units = Unit::all(); // Fetch all units
        $categories = Category::all(); // Fetch all categories
        return view('product.create', [
            'action' => route('product.store'),
            'method' => 'POST',
            'product' => null,
            'categories' => $categories,
            'units' => $units,
            'outlets' => $outlets,
            'selectedOutlets' => [], // No pre-selected outlets for create
            'selectedUnit' => null, // No pre-selected units for create
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
            'base_price' => 'required|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'min_qty' => 'required|integer|min:0',
            'units_id' => 'required|exists:units,id',
            'categories_id' => 'required|exists:categories,id',
            'is_shown' => 'required|boolean',
            'outlets' => 'nullable|array', // Outlets can be null or an array
            'outlets.*' => 'exists:outlets,id', // Ensure each outlet exists
        ]);

        // Create the product
        $product = Product::create([
            'sku' => $validatedData['sku'],
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
            'base_price' => $validatedData['base_price'],
            'buy_price' => $validatedData['buy_price'],
            'sell_price' => $validatedData['sell_price'],
            'status' => $validatedData['status'],
            'min_qty' => $validatedData['min_qty'],
            'units_id' => $validatedData['units_id'],
            'categories_id' => $validatedData['categories_id'],
            'is_shown' => $validatedData['is_shown'],
        ]);

        // Attach outlets to the product (if any)
        if (!empty($validatedData['outlets'])) {
            $product->outlets()->sync($validatedData['outlets']);
        }

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
        $outlets = Outlet::all(); // Fetch all outlets
        $selectedOutlets = $product->outlets->pluck('id')->toArray(); // Get selected outlet IDs
        $units = Unit::all(); // Fetch all units
        $categories = Category::all(); // Fetch all categories
        return view('product.edit', [
            'action' => route('product.update', $product->sku),
            'method' => 'PUT',
            'product' => $product,
            'units' => $units,
            'categories' => $categories,
            'outlets' => $outlets,
            'selectedOutlets' => $selectedOutlets,
            'selectedUnit' => $product->unit,
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
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->sku . ',sku',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'min_qty' => 'required|integer|min:0',
            'units_id' => 'required|exists:units,id',
            'categories_id' => 'required|exists:categories,id',
            'is_shown' => 'required|boolean',
            'outlets' => 'nullable|array', // Outlets can be null or an array
            'outlets.*' => 'exists:outlets,id', // Ensure each outlet exists
        ]);

        $product->update([
            'sku' => $validatedData['sku'],
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
            'buy_price' => $validatedData['buy_price'],
            'sell_price' => $validatedData['sell_price'],
            'min_qty' => $validatedData['min_qty'],
            'units_id' => $validatedData['units_id'],
            'categories_id' => $validatedData['categories_id'],
            'is_shown' => $validatedData['is_shown'],
        ]);

        // Sync outlets with the product (if any)
        if (!empty($validatedData['outlets'])) {
            $product->outlets()->sync($validatedData['outlets']);
        } else {
            $product->outlets()->detach(); // Detach all outlets if none are provided
        }

        // Redirect back to the product index with a success message
        return redirect()->route('product.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Perform soft delete
        $product->delete();

        // Redirect back to the product index with a success message
        return redirect()->route('product.index')->with('success', 'Product deleted successfully.');
    }
}
