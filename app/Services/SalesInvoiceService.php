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
                'outlets_id' => $data['outlets'][0],
                'invoice_number' => $data['invoice_number'],
                'grand_total' => $data['grand_total'],
                'description' => $data['description'],
                'nip' => $data['nip'],
            ]);

            $products = [];
            foreach ($data['products'] as $product) {
                $products[$product['sku']] = [
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['unit_price'],
                    'total_price' => $product['quantity'] * $product['unit_price'],
                ];
            }
            $salesInvoice->products()->attach($products);

            return $salesInvoice;
        });
    }

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
                'outlets_id' => $data['outlets'][0],
                'invoice_number' => $data['invoice_number'],
                'grand_total' => $data['grand_total'],
                'description' => $data['description'],
                'nip' => $data['nip'],
            ]);

            $products = [];
            foreach ($data['products'] as $product) {
                $products[$product['sku']] = [
                    'quantity' => $product['quantity'],
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