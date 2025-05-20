<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Services\PurchaseInvoiceService;
use App\Services\OutletService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
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
        $this->middleware('permission:purchase.view')->only(['index', 'show']);
        $this->middleware('permission:purchase.create')->only(['create', 'store']);
        $this->middleware('permission:purchase.edit')->only(['edit', 'update']);
        $this->middleware('permission:purchase.delete')->only(['destroy']);

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
        return view('invoice.index', [
            'invoiceType' => 'Purchase',
            'invoices' => $purchaseInvoices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = $this->outletService->getAllOutlets();
        $products = $this->outletService->getProductsWithStocksFromOutlet($outlets[0]->id ?? null);
        $nextInvoiceNumber = $this->purchaseInvoiceService->generatePurchaseInvoiceNumber();
        return view('invoice.create', [
            'action' => route('purchase.store'),
            'method' => 'POST',
            'invoiceType' => 'Purchase',
            'invoice' => null,
            'outlets' => $outlets,
            'products' => $products,
            'nextInvoiceNumber' => $nextInvoiceNumber,
            'cancelRoute' => route('purchase.index'),
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
            'outlet_id' => 'required|exists:outlets,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'employee_id' => 'required|numeric',
        ]);

        // Use the service to create the purchase invoice
        $this->purchaseInvoiceService->createPurchaseInvoice($validatedData);

        // Redirect back with a success message
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
    public function edit(PurchaseInvoice $purchase)
    {
        $outlets = $this->outletService->getAllOutlets();
        $products = $this->productService->getAllProducts();
        return view('invoice.edit', [
            'action' => route('purchase.update', $purchase->id),
            'method' => 'PUT',
            'invoiceType' => 'Purchase',
            'invoice' => $purchase,
            'outlets' => $outlets,
            'products' => $products,
            'cancelRoute' => route('purchase.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseInvoice $purchase)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:purchase_invoices,invoice_number,' . $purchase->id,
            'grand_total' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'outlet_id' => 'required|exists:outlets,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'employee_id' => 'required|numeric',
        ]);

        // Use the service to update the purchase invoice
        $this->purchaseInvoiceService->updatePurchaseInvoice($purchase, $validatedData);

        // Redirect back with a success message
        return redirect()->route('purchase.index')->with('success', 'Purchase invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseInvoice $purchase)
    {
        // Use the service to delete the purchase invoice
        $this->purchaseInvoiceService->deletePurchaseInvoice($purchase);

        // Redirect back with a success message
        return redirect()->route('purchase.index')->with('success', 'Purchase invoice deleted successfully.');
    }
}
