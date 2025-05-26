<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Services\AccessControlService;
use App\Services\InventoryService;
use App\Services\SalesInvoiceService;
use App\Services\OutletService;
use App\Services\ProductService;
use App\Services\StockMovementService;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    protected $accessControlService;

    /**
     * Constructor to inject the services.
     */
    public function __construct(
        protected InventoryService $inventoryService,
        protected SalesInvoiceService $salesInvoiceService,
        protected OutletService $outletService,
        protected ProductService $productService
    ) {
        $this->middleware('permission:sales.view|sales.*')->only(['index', 'show']);
        $this->middleware('permission:sales.create|sales.*')->only(['create', 'store']);
        $this->middleware('permission:sales.edit|sales.*')->only(['edit', 'update']);
        $this->middleware('permission:sales.delete|sales.*')->only(['destroy']);

        $this->accessControlService = app(AccessControlService::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $selectedOutletId = session('selected_outlet_id');
        return view('invoice.index', [
            'invoiceType' => 'Sales',
            'selectedOutletId' => $selectedOutletId,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = $this->outletService->getAllOutlets();
        $products = $this->inventoryService->getStocksByOutlet($outlets->first()->id);
        $nextInvoiceNumber = $this->salesInvoiceService->generateSalesInvoiceNumber();
        return view('invoice.create', [
            'action' => route('sales.store'),
            'method' => 'POST',
            'invoiceType' => 'Sales',
            'invoice' => null,
            'outlets' => $outlets,
            'products' => $products,
            'nextInvoiceNumber' => $nextInvoiceNumber,
            'cancelRoute' => route('sales.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:sales_invoices,invoice_number',
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

        $this->salesInvoiceService->createSalesInvoice($validatedData);
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
    public function edit(SalesInvoice $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesInvoice $sale)
    {
        //
    }

    public function void(Request $request, SalesInvoice $sale)
    {
        $validatedData = $request->validate([
            'void_reason' => 'nullable|string|max:255',
        ]);

        $voidedBy = $this->accessControlService->getUser()->id;
        try {
            $sale->void($validatedData['void_reason'] ?? 'No reason provided', $voidedBy, app(StockMovementService::class));

            return redirect()->route('sales.index')
                ->with('success', 'Invoice #' . $sale->invoice_number . ' has been voided successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to void invoice: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesInvoice $sale)
    {
        $this->salesInvoiceService->deleteSalesInvoice($sale);
        return redirect()->route('sales.index')->with('success', 'Sales invoice deleted successfully.');
    }
}
