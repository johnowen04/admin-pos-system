<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Services\InventoryService;
use App\Services\PurchaseInvoiceService;
use App\Services\OutletService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected $inventoryService;
    protected $outletService;
    protected $productService;
    protected $purchaseInvoiceService;

    /**
     * Constructor to inject the services.
     */
    public function __construct(
        InventoryService $inventoryService,
        OutletService $outletService,
        PurchaseInvoiceService $purchaseInvoiceService,
        ProductService $productService
    ) {
        $this->middleware('permission:purchase.view|purchase.*')->only(['index', 'show']);
        $this->middleware('permission:purchase.create|purchase.*')->only(['create', 'store']);
        $this->middleware('permission:purchase.edit|purchase.*')->only(['edit', 'update']);
        $this->middleware('permission:purchase.delete|purchase.*')->only(['destroy']);

        $this->inventoryService = $inventoryService;
        $this->outletService = $outletService;
        $this->purchaseInvoiceService = $purchaseInvoiceService;
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $selectedOutletId = session('selected_outlet_id');
        if ($selectedOutletId == 'all' || $selectedOutletId == null) {
            $purchaseInvoices = $this->purchaseInvoiceService->getAllPurchaseInvoices();
        } else {
            $purchaseInvoices = $this->purchaseInvoiceService->getPurchaseInvoicesByOutletId($selectedOutletId);
        }
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
        $selectedOutletId = session('selected_outlet_id');
        $outlets = $this->outletService->getAllOutlets();

        if ($selectedOutletId == 'all' || $selectedOutletId == null) {
            $products = $this->inventoryService->getStocksAllOutlet();
        } else {
            $products = $this->inventoryService->getStocksByOutlet($selectedOutletId);
        }

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
            'products.*.base_price' => 'required|numeric|min:0',
            'products.*.unit_price' => 'required|numeric|min:0',
            'employee_id' => 'nullable|numeric',
            'created_by' => 'required|numeric',
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
        $products = $this->inventoryService->getStocksByOutlet($purchase->outlet_id);
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
            'products.*.base_price' => 'required|numeric|min:0',
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
