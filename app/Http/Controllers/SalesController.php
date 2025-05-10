<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = SalesInvoice::all();
        return view('sales.index', [
            'sales' => $sales, // Placeholder for sales
            'createRoute' => route('sales.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();  // Fetch all products
        $outlets = Outlet::all(); // Fetch all outlets
        return view('sales.create', [
            'action' => route('sales.store'),
            'method' => 'POST',
            'salesInvoice' => null,
            'outlets' => $outlets,
            'products' => $products,
            'cancelRoute' => route('sales.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'outlets' => 'required|array',
            'invoice_number' => 'required|string|unique:sales_invoices,invoice_number',
            'grand_total' => 'required|numeric|min:0', // Validate grand total
            'description' => 'nullable|string',
            'nip' => 'required|string',
            'products' => 'required|array',
            'products.*.sku' => 'required|string|exists:products,sku',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Create the sales invoice
        $salesInvoice = SalesInvoice::create([
            'outlets_id' => $validatedData['outlets'][0], // Assuming single outlet selection
            'invoice_number' => $validatedData['invoice_number'],
            'grand_total' => $validatedData['grand_total'],
            'description' => $validatedData['description'],
            'nip' => $validatedData['nip'],
        ]);

        // Attach products to the sales invoice
        foreach ($validatedData['products'] as $product) {
            $salesInvoice->products()->attach($product['sku'], [
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
                'total_price' => $product['quantity'] * $product['unit_price'],
            ]);

            $product = Product::find($product['sku']);

            // Check if the product already exists in the outlet
            $existingQuantity = $product->outlets()
                ->where('outlets_id', $validatedData['outlets'][0])
                ->first()
                ->pivot
                ->quantity ?? 0;

            // Update or create the stock entry
            $product->outlets()->syncWithoutDetaching([
                $validatedData['outlets'][0] => [
                    'quantity' => $existingQuantity + $product['quantity'],
                ],
            ]);
        }

        return redirect()->route('sales.index')->with('success', 'Sales invoice created successfully.');
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
    public function edit(string $id)
    {
        // Fetch the sales invoice and related data
        $salesInvoice = SalesInvoice::with('products')->findOrFail($id);
        $products = Product::all();
        $outlets = Outlet::all();

        return view('sales.edit', [
            'action' => route('sales.update', $id),
            'method' => 'PUT',
            'salesInvoice' => $salesInvoice,
            'outlets' => $outlets,
            'products' => $products,
            'cancelRoute' => route('sales.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request
        $validatedData = $request->validate([
            'outlets' => 'required|array',
            'invoice_number' => 'required|string|unique:sales_invoices,invoice_number,' . $id,
            'grand_total' => 'required|numeric|min:0', // Validate grand total
            'description' => 'nullable|string',
            'nip' => 'required|string',
            'products' => 'required|array',
            'products.*.sku' => 'required|string|exists:products,sku',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Find the sales invoice
        $salesInvoice = SalesInvoice::findOrFail($id);

        // Update the sales invoice
        $salesInvoice->update([
            'outlets_id' => $validatedData['outlets'][0], // Assuming single outlet selection
            'invoice_number' => $validatedData['invoice_number'],
            'grand_total' => $validatedData['grand_total'],
            'description' => $validatedData['description'],
            'nip' => $validatedData['nip'],
        ]);

        // Sync products with the sales invoice
        $products = [];
        foreach ($validatedData['products'] as $product) {
            $products[$product['sku']] = [
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
                'total_price' => $product['quantity'] * $product['unit_price'],
            ];

            $product = Product::find($product['sku']);

            // Check if the product already exists in the outlet
            $existingQuantity = $product->outlets()
                ->where('outlets_id', $validatedData['outlets'][0])
                ->first()
                ->pivot
                ->quantity ?? 0;

            // Update or create the stock entry
            $product->outlets()->syncWithoutDetaching([
                $validatedData['outlets'][0] => [
                    'quantity' => $existingQuantity + $product['quantity'],
                ],
            ]);
        }
        $salesInvoice->products()->sync($products);

        return redirect()->route('sales.index')->with('success', 'Sales invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find and delete the sales invoice
        $salesInvoice = SalesInvoice::findOrFail($id);
        $salesInvoice->delete();

        return redirect()->route('sales.index')->with('success', 'Sales invoice deleted successfully.');
    }
}
