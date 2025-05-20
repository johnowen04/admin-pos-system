<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Services\SalesInvoiceService;
use App\Services\OutletService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    protected $salesInvoiceService;
    protected $outletService;
    protected $productService;

    /**
     * Constructor to inject the services.
     */
    public function __construct(
        SalesInvoiceService $salesInvoiceService,
        OutletService $outletService,
        ProductService $productService
    ) {
        $this->middleware('permission:sales.view')->only(['index', 'show']);
        $this->middleware('permission:sales.create')->only(['create', 'store']);
        $this->middleware('permission:sales.edit')->only(['edit', 'update']);
        $this->middleware('permission:sales.delete')->only(['destroy']);

        $this->salesInvoiceService = $salesInvoiceService;
        $this->outletService = $outletService;
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salesInvoices = $this->salesInvoiceService->getAllSalesInvoices();
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
        $outlets = $this->outletService->getAllOutlets();
        $products = $this->outletService->getProductsWithStocksFromOutlet($outlets[0]->id ?? null);
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
        // Validate the incoming request
        $validatedData = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:sales_invoices,invoice_number',
            'grand_total' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'outlet_id' => 'required|exists:outlets,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'employee_id' => 'required|numeric',
        ]);

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
            'action' => route('sales.update', $sale->id),
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
            'products.*.unit_price' => 'required|numeric|min:0',
            'employee_id' => 'required|numeric',
        ]);

        // Use the service to update the sales invoice
        $this->salesInvoiceService->updateSalesInvoice($sale, $validatedData);

        // Redirect back with a success message
        return redirect()->route('sales.index')->with('success', 'Sales invoice updated successfully.');
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
