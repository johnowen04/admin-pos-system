<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\PurchaseInvoices;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = PurchaseInvoices::all();
        return view('purchase.index', [
            'purchases' => $purchases, // Placeholder for purchases
            'createRoute' => route('purchase.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();  // Fetch all products
        $outlets = Outlet::all(); // Fetch all outlets
        return view('purchase.create', [
            'action' => route('purchase.store'),
            'method' => 'POST',
            'purchase' => null,
            'outlets' => $outlets,
            'products' => $products,
            'cancelRoute' => route('purchase.index'),
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
            'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number',
            'grand_total' => 'required|numeric|min:0', // Validate grand total
            'description' => 'nullable|string',
            'nip' => 'required|string',
            'products' => 'required|array',
            'products.*.sku' => 'required|string|exists:products,sku',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Create the purchase invoice
        $purchaseInvoice = PurchaseInvoices::create([
            'outlets_id' => $validatedData['outlets'][0], // Assuming single outlet selection
            'invoice_number' => $validatedData['invoice_number'],
            'grand_total' => $validatedData['grand_total'],
            'description' => $validatedData['description'],
            'nip' => $validatedData['nip'],
        ]);

        // Attach products to the purchase invoice
        foreach ($validatedData['products'] as $product) {
            $purchaseInvoice->products()->attach($product['sku'], [
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
                'total_price' => $product['quantity'] * $product['unit_price'],
            ]);

            InventoryService::syncProductStock($product['sku'], $validatedData['outlets'][0], $product['quantity']);
        }

        return redirect()->route('purchase.index')->with('success', 'Purchase invoice created successfully.');
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
        // Fetch the purchase invoice and related data
        $purchaseInvoice = PurchaseInvoices::with('products')->findOrFail($id);
        $products = Product::all();
        $outlets = Outlet::all();

        return view('purchase.edit', [
            'action' => route('purchase.update', $id),
            'method' => 'PUT',
            'purchaseInvoice' => $purchaseInvoice,
            'outlets' => $outlets,
            'products' => $products,
            'cancelRoute' => route('purchase.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request
        $validated = $request->validate([
            'outlets' => 'required|array',
            'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number,' . $id,
            'grand_total' => 'required|numeric|min:0', // Validate grand total
            'description' => 'nullable|string',
            'nip' => 'required|string',
            'products' => 'required|array',
            'products.*.sku' => 'required|string|exists:products,sku',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Find the purchase invoice
        $purchaseInvoice = PurchaseInvoices::findOrFail($id);

        // Update the purchase invoice
        $purchaseInvoice->update([
            'outlets_id' => $validated['outlets'][0], // Assuming single outlet selection
            'invoice_number' => $validated['invoice_number'],
            'grand_total' => $validated['grand_total'],
            'description' => $validated['description'],
            'nip' => $validated['nip'],
        ]);

        // Sync products with the purchase invoice
        $products = [];
        foreach ($validated['products'] as $product) {
            $products[$product['sku']] = [
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
                'total_price' => $product['quantity'] * $product['unit_price'],
            ];

            $product = Product::find($product['sku']);

            // Check if the product already exists in the outlet
            $existingQuantity = $product->outlets()
                ->where('outlets_id', $validated['outlets'][0])
                ->first()
                ->pivot
                ->quantity ?? 0;

            // Update or create the stock entry
            $product->outlets()->syncWithoutDetaching([
                $validated['outlets'][0] => [
                    'quantity' => $existingQuantity + $product['quantity'],
                ],
            ]);
        }
        $purchaseInvoice->products()->sync($products);

        return redirect()->route('purchase.index')->with('success', 'Purchase invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find and delete the purchase invoice
        $purchaseInvoice = PurchaseInvoices::findOrFail($id);
        $purchaseInvoice->delete();

        return redirect()->route('purchase.index')->with('success', 'Purchase invoice deleted successfully.');
    }
}
