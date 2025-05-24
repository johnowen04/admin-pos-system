<?php

namespace App\Services;

use App\Helpers\InvoiceNumberGenerator;
use App\Models\SalesInvoice;
use Illuminate\Support\Facades\DB;

class SalesInvoiceService
{
    /**
     * Get all sales invoices.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSalesInvoices()
    {
        return SalesInvoice::with(['products', 'outlet'])->get();
    }

    public function getSalesInvoicesByOutletId(int $outletId)
    {
        return SalesInvoice::where('outlet_id', $outletId)
            ->with(['outlet', 'employee', 'products'])
            ->get();
    }


    public function getSalesInvoiceById($id)
    {
        return SalesInvoice::with(['products', 'outlet'])->findOrFail($id);
    }

    /**
     * Create a new sales invoice.
     *
     * @param array $data
     * @return \App\Models\SalesInvoice
     */
    public function createSalesInvoice(array $data)
    {
        return DB::transaction(function () use ($data) {
            $salesInvoice = SalesInvoice::create([
                'outlet_id' => $data['outlet_id'], // Assuming one outlet is selected
                'invoice_number' => $data['invoice_number'],
                'description' => $data['description'] ?? null,
                'grand_total' => $data['grand_total'],
                'employee_id' => $data['employee_id'] ?? null,
                'created_by' => $data['created_by'],
            ]);

            // Attach products to the purchase invoice
            if (!empty($data['products'])) {
                $products = [];
                foreach ($data['products'] as $product) {
                    $products[$product['id']] = [
                        'quantity' => $product['quantity'],
                        'base_price' => $product['base_price'],
                        'unit_price' => $product['unit_price'],
                        'total_price' => $product['quantity'] * $product['unit_price'],
                    ];
                }
                $salesInvoice->products()->attach($products);
            }

            // Record stock movements for each product
            foreach ($data['products'] as $product) {
                app(StockMovementService::class)->recordSale(
                    $data['outlet_id'], // Assuming one outlet is selected
                    $product['id'],
                    $data['employee_id'],
                    $product['quantity']
                );
            }

            return $salesInvoice;
        });
    }

    // Update -> Void
    /**
     * Update an existing sales invoice.
     *
     * @param \App\Models\SalesInvoice $salesInvoice
     * @param array $data
     * @return bool
     */
    public function updateSalesInvoice(SalesInvoice $salesInvoice, array $data)
    {
        return DB::transaction(function () use ($salesInvoice, $data) {
            $salesInvoice->update([
                'outlet_id' => $data['outlet_id'], // Assuming one outlet is selected
                'invoice_number' => $data['invoice_number'],
                'description' => $data['description'] ?? null,
                'grand_total' => $data['grand_total'],
                'employee_id' => $data['employee_id'],
            ]);

            $products = [];
            foreach ($data['products'] as $product) {
                $products[$product['id']] = [
                    'quantity' => $product['quantity'],
                    'base_price' => $product['base_price'],
                    'unit_price' => $product['unit_price'],
                    'total_price' => $product['quantity'] * $product['unit_price'],
                ];
            }
            $salesInvoice->products()->sync($products);

            return true;
        });
    }

    /**
     * Delete a sales invoice.
     *
     * @param \App\Models\SalesInvoice $salesInvoice
     * @return bool|null
     */
    public function deleteSalesInvoice(SalesInvoice $salesInvoice)
    {
        return $salesInvoice->delete();
    }


    /**
     * Generate a new sales invoice number.
     *
     * @return string
     */
    public function generateSalesInvoiceNumber()
    {
        return InvoiceNumberGenerator::generate('SO', SalesInvoice::class);
    }
}