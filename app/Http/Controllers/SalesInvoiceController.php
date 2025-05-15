<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Services\SalesInvoiceService;
use App\Services\OutletService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class SalesInvoiceController extends Controller
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
        return view('sales.index', [
            'salesInvoices' => $salesInvoices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = $this->outletService->getAllOutlets();
        $products = $this->productService->getAllProducts();
        $nextInvoiceNumber = $this->salesInvoiceService->generateSalesInvoiceNumber();
        return view('sales.create', [
            'action' => route('sales-invoice.store'),
            'method' => 'POST',
            'salesInvoice' => null,
            'outlets' => $outlets,
            'products' => $products,
            'nextInvoiceNumber' => $nextInvoiceNumber,
            'cancelRoute' => route('sales-invoice.index'),
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
            'outlets' => 'required|array',
            'outlets.*' => 'exists:outlets,id',
            'products' => 'required|array',
            'products.*.sku' => 'required|exists:products,sku',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'nip' => 'required|string|max:50',
        ]);

        // Use the service to create the sales invoice
        $this->salesInvoiceService->createSalesInvoice($validatedData);

        // Redirect back with a success message
        return redirect()->route('sales-invoice.index')->with('success', 'Sales invoice created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesInvoice $salesInvoice)
    {
        $outlets = $this->outletService->getAllOutlets();
        $products = $this->productService->getAllProducts();
        return view('sales.edit', [
            'action' => route('sales-invoice.update', $salesInvoice->id),
            'method' => 'PUT',
            'salesInvoice' => $salesInvoice,
            'outlets' => $outlets,
            'products' => $products,
            'cancelRoute' => route('sales-invoice.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesInvoice $salesInvoice)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:sales_invoices,invoice_number,' . $salesInvoice->id,
            'grand_total' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'outlets' => 'required|array',
            'outlets.*' => 'exists:outlets,id',
            'products' => 'required|array',
            'products.*.sku' => 'required|exists:products,sku',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'nip' => 'required|string|max:50',
        ]);

        // Use the service to update the sales invoice
        $this->salesInvoiceService->updateSalesInvoice($salesInvoice, $validatedData);

        // Redirect back with a success message
        return redirect()->route('sales-invoice.index')->with('success', 'Sales invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesInvoice $salesInvoice)
    {
        // Use the service to delete the sales invoice
        $this->salesInvoiceService->deleteSalesInvoice($salesInvoice);

        // Redirect back with a success message
        return redirect()->route('sales-invoice.index')->with('success', 'Sales invoice deleted successfully.');
    }
}