<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Services\AccessControlService;
use App\Services\InventoryService;
use App\Services\PurchaseInvoiceService;
use App\Services\OutletService;
use App\Services\ProductService;
use App\Services\StockMovementService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected $inventoryService;
    protected $outletService;
    protected $productService;
    protected $purchaseInvoiceService;
    protected $accessControlService;

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
        $this->middleware('permission:purchase.delete|purchase.*')->only(['void', 'destroy']);

        $this->inventoryService = $inventoryService;
        $this->outletService = $outletService;
        $this->purchaseInvoiceService = $purchaseInvoiceService;
        $this->productService = $productService;

        $this->accessControlService = app(AccessControlService::class);
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
            'selectedOutletId' => $selectedOutletId,
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
        ]);

        $validatedData['created_by'] = $this->accessControlService->getUser()->id;
        $validatedData['employee_id'] = $this->accessControlService->getUser()->employee ? $this->accessControlService->getUser()->employee->id : null; // Assuming the employee is the current user

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
            'action' => route('purchase.void', $purchase->id),
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
        ]);

        // Use the service to update the purchase invoice
        $this->purchaseInvoiceService->updatePurchaseInvoice($purchase, $validatedData);

        // Redirect back with a success message
        return redirect()->route('purchase.index')->with('success', 'Purchase invoice updated successfully.');
    }

    public function void(Request $request, PurchaseInvoice $purchase)
    {
        // Validate optional reason input from form
        $validatedData = $request->validate([
            'void_reason' => 'nullable|string|max:255',
        ]);

        $voidedBy = $this->accessControlService->getUser()->id;
        try {
            $purchase->void($validatedData['void_reason'] ?? 'No reason provided', $voidedBy, app(StockMovementService::class));

            return redirect()->route('purchase.index')
                ->with('success', 'Invoice #' . $purchase->invoice_number . ' has been voided successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to void invoice: ' . $e->getMessage()]);
        }
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
