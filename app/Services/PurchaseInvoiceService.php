<?php

namespace App\Services;

use App\Helpers\InvoiceNumberGenerator;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceService
{
    /**
     * Get all purchase invoices.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPurchaseInvoices()
    {
        return PurchaseInvoice::with(['outlet', 'employee', 'products'])->get();
    }

    /**
     * Create a new purchase invoice.
     *
     * @param array $data
     * @return \App\Models\PurchaseInvoice
     */
    public function createPurchaseInvoice(array $data)
    {
        DB::transaction(function () use ($data, &$purchaseInvoice) {
            // Create the purchase invoice
            $purchaseInvoice = PurchaseInvoice::create([
                'invoice_number' => $data['invoice_number'],
                'grand_total' => $data['grand_total'],
                'description' => $data['description'] ?? null,
                'outlets_id' => $data['outlets'][0], // Assuming one outlet is selected
                'nip' => $data['nip'],
            ]);

            // Attach products to the purchase invoice
            if (!empty($data['products'])) {
                $products = [];
                foreach ($data['products'] as $product) {
                    $products[$product['sku']] = [
                        'quantity' => $product['quantity'],
                        'unit_price' => $product['unit_price'],
                        'total_price' => $product['quantity'] * $product['unit_price'],
                    ];
                }
                $purchaseInvoice->products()->sync($products);
            }
        });

        return $purchaseInvoice;
    }

    /**
     * Update an existing purchase invoice.
     *
     * @param \App\Models\PurchaseInvoice $purchaseInvoice
     * @param array $data
     * @return bool
     */
    public function updatePurchaseInvoice(PurchaseInvoice $purchaseInvoice, array $data)
    {
        DB::transaction(function () use ($purchaseInvoice, $data) {
            // Update the purchase invoice
            $purchaseInvoice->update([
                'invoice_number' => $data['invoice_number'],
                'grand_total' => $data['grand_total'],
                'description' => $data['description'] ?? null,
                'outlets_id' => $data['outlets'][0], // Assuming one outlet is selected
            ]);

            // Update products associated with the purchase invoice
            if (!empty($data['products'])) {
                $products = [];
                foreach ($data['products'] as $product) {
                    $products[$product['sku']] = [
                        'quantity' => $product['quantity'],
                        'unit_price' => $product['unit_price'],
                        'total_price' => $product['quantity'] * $product['unit_price'],
                    ];
                }
                $purchaseInvoice->products()->sync($products);
            }
        });

        return true;
    }

    /**
     * Delete a purchase invoice.
     *
     * @param \App\Models\PurchaseInvoice $purchaseInvoice
     * @return bool|null
     */
    public function deletePurchaseInvoice(PurchaseInvoice $purchaseInvoice)
    {
        return $purchaseInvoice->delete();
    }

    /**
     * Get a purchase invoice by ID.
     *
     * @param int $id
     * @return \App\Models\PurchaseInvoice|null
     */
    public function getPurchaseInvoiceById(int $id)
    {
        return PurchaseInvoice::with(['outlet', 'employee', 'products'])->find($id);
    }

    /**
     * Generate a new purchase invoice number.
     *
     * @return string
     */
    public function generatePurchaseInvoiceNumber()
    {
        return InvoiceNumberGenerator::generate('PO', PurchaseInvoice::class);
    }
}
