<?php

namespace App\Http\Controllers;


use App\Models\PurchaseInvoice;
use App\Services\PurchaseInvoiceService;
use App\Services\OutletService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    protected $purchaseInvoiceService;
    protected $outletService;
    protected $productService;

    /**
     * Constructor to inject the services.
     */
    public function __construct(
        PurchaseInvoiceService $purchaseInvoiceService,
        OutletService $outletService,
        ProductService $productService
    ) {
        $this->purchaseInvoiceService = $purchaseInvoiceService;
        $this->outletService = $outletService;
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseInvoices = $this->purchaseInvoiceService->getAllPurchaseInvoices();
        return view('purchase.index', [
            'purchaseInvoices' => $purchaseInvoices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = $this->outletService->getAllOutlets();
        $products = $this->productService->getAllProducts();
        $nextInvoiceNumber = $this->purchaseInvoiceService->generatePurchaseInvoiceNumber();
        return view('purchase.create', [
            'action' => route('purchase-invoice.store'),
            'method' => 'POST',
            'purchaseInvoice' => null,
            'outlets' => $outlets,
            'products' => $products,
            'nextInvoiceNumber' => $nextInvoiceNumber,
            'cancelRoute' => route('purchase-invoice.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:purchase_invoices,invoice_number',
            'grand_total' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'outlets' => 'required|array',
            'outlets.*' => 'exists:outlets,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'employee_id' => 'required|numeric',
        ]);

        // Use the service to create the purchase invoice
        $this->purchaseInvoiceService->createPurchaseInvoice($validatedData);

        // Redirect back with a success message
        return redirect()->route('purchase-invoice.index')->with('success', 'Purchase invoice created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseInvoice $purchaseInvoice)
    {
        $outlets = $this->outletService->getAllOutlets();
        $products = $this->productService->getAllProducts();
        return view('purchase.edit', [
            'action' => route('purchase-invoice.update', $purchaseInvoice->id),
            'method' => 'PUT',
            'purchaseInvoice' => $purchaseInvoice,
            'outlets' => $outlets,
            'products' => $products,
            'cancelRoute' => route('purchase-invoice.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:purchase_invoices,invoice_number,' . $purchaseInvoice->id,
            'grand_total' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'outlets' => 'required|array',
            'outlets.*' => 'exists:outlets,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'employee_id' => 'required|numeric',
        ]);

        // Use the service to update the purchase invoice
        $this->purchaseInvoiceService->updatePurchaseInvoice($purchaseInvoice, $validatedData);

        // Redirect back with a success message
        return redirect()->route('purchase-invoice.index')->with('success', 'Purchase invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        // Use the service to delete the purchase invoice
        $this->purchaseInvoiceService->deletePurchaseInvoice($purchaseInvoice);

        // Redirect back with a success message
        return redirect()->route('purchase-invoice.index')->with('success', 'Purchase invoice deleted successfully.');
    }
}
