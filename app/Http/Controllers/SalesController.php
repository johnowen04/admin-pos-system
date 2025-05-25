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
    protected $inventoryService;
    protected $outletService;
    protected $productService;
    protected $salesInvoiceService;
    protected $accessControlService;

    /**
     * Constructor to inject the services.
     */
    public function __construct(
        InventoryService $inventoryService,
        SalesInvoiceService $salesInvoiceService,
        OutletService $outletService,
        ProductService $productService
    ) {
        $this->middleware('permission:sales.view|sales.*')->only(['index', 'show']);
        $this->middleware('permission:sales.create|sales.*')->only(['create', 'store']);
        $this->middleware('permission:sales.edit|sales.*')->only(['edit', 'update']);
        $this->middleware('permission:sales.delete|sales.*')->only(['destroy']);

        $this->inventoryService = $inventoryService;
        $this->outletService = $outletService;
        $this->productService = $productService;
        $this->salesInvoiceService = $salesInvoiceService;

        $this->accessControlService = app(AccessControlService::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $selectedOutletId = session('selected_outlet_id');
        if ($selectedOutletId == 'all' || $selectedOutletId == null) {
            $salesInvoices = $this->salesInvoiceService->getAllSalesInvoices();
        } else {
            $salesInvoices = $this->salesInvoiceService->getSalesInvoicesByOutletId($selectedOutletId);
        }
        return view('invoice.index', [
            'invoiceType' => 'Sales',
            'invoices' => $salesInvoices,
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

        $nextInvoiceNumber = $this->salesInvoiceService->generateSalesInvoiceNumber();
        return view('invoice.create', [
            'action' => route('sales.store'),
            'method' => 'POST',
            'invoiceType' => 'Sales',
            'invoice' => null,
            'outlets' => $outlets,
            'products' => $products,
            'selectedOutletId' => $selectedOutletId,
            'nextInvoiceNumber' => $nextInvoiceNumber,
            'cancelRoute' => route('sales.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
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
        $validatedData['employee_id'] = $this->accessControlService->getUser()->employee ? $this->accessControlService->getUser()->employee->id : null; // Assuming the employee is the current user

        // Use the service to create the sales invoice
        $this->salesInvoiceService->createSalesInvoice($validatedData);

        // Redirect back with a success message
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
        $outlets = $this->outletService->getAllOutlets();
        $products = $this->outletService->getProductsWithStocksFromOutlet($outlets[0]->id ?? null);
        return view('invoice.edit', [
            'action' => route('sales.void', $sale->id),
            'method' => 'PUT',
            'invoiceType' => 'Sales',
            'invoice' => $sale,
            'outlets' => $outlets,
            'products' => $products,
            'cancelRoute' => route('sales.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesInvoice $sale)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:sales_invoices,invoice_number,' . $sale->id,
            'grand_total' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'outlet_id' => 'required|exists:outlets,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.base_price' => 'required|numeric|min:0',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Use the service to update the sales invoice
        $this->salesInvoiceService->updateSalesInvoice($sale, $validatedData);

        // Redirect back with a success message
        return redirect()->route('sales.index')->with('success', 'Sales invoice updated successfully.');
    }

    public function void(Request $request, SalesInvoice $sale)
    {
        // Validate optional reason input from form
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
        // Use the service to delete the sales invoice
        $this->salesInvoiceService->deleteSalesInvoice($sale);

        // Redirect back with a success message
        return redirect()->route('sales.index')->with('success', 'Sales invoice deleted successfully.');
    }
}
