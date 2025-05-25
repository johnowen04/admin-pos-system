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
    protected $accessControlService;

    /**
     * Constructor to inject the services.
     */
    public function __construct(
        protected InventoryService $inventoryService,
        protected OutletService $outletService,
        protected PurchaseInvoiceService $purchaseInvoiceService,
        protected ProductService $productService
    ) {
        $this->middleware('permission:purchase.view|purchase.*')->only(['index', 'show']);
        $this->middleware('permission:purchase.create|purchase.*')->only(['create', 'store']);
        $this->middleware('permission:purchase.edit|purchase.*')->only(['edit', 'update']);
        $this->middleware('permission:purchase.delete|purchase.*')->only(['void', 'destroy']);

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
        $outlets = $this->outletService->getAllOutlets();
        $products = $this->inventoryService->getStocksByOutlet($outlets->first()->id);
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
        $validatedData['employee_id'] = $this->accessControlService->getUser()->employee ? $this->accessControlService->getUser()->employee->id : null;

        $this->purchaseInvoiceService->createPurchaseInvoice($validatedData);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseInvoice $purchase)
    {
        //
    }

    public function void(Request $request, PurchaseInvoice $purchase)
    {
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
        $this->purchaseInvoiceService->deletePurchaseInvoice($purchase);
        return redirect()->route('purchase.index')->with('success', 'Purchase invoice deleted successfully.');
    }
}
