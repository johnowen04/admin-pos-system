<?php

namespace App\Services;

use App\Helpers\InvoiceNumberGenerator;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceService
{
    protected $productService;
    protected $inventoryService;

    public function __construct(ProductService $productService, InventoryService $inventoryService)
    {
        $this->productService = $productService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Get all purchase invoices.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPurchaseInvoices()
    {
        return PurchaseInvoice::with(['outlet', 'employee', 'products'])->get();
    }

    public function getPurchaseInvoicesByOutletId(int $outletId)
    {
        return PurchaseInvoice::where('outlet_id', $outletId)
            ->with(['outlet', 'employee', 'products'])
            ->get();
    }

    public function getPreviousPurchasesForProduct(int $productId)
    {
        $product = $this->productService->getProductById($productId);

        return $product->purchaseInvoices()
            ->withPivot(['quantity', 'unit_price'])
            ->get()
            ->map(function ($invoice) {
                return (object) [
                    'quantity' => $invoice->pivot->quantity,
                    'unit_price' => $invoice->pivot->unit_price
                ];
            });
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
                'outlet_id' => $data['outlet_id'], // Assuming one outlet is selected
                'invoice_number' => $data['invoice_number'],
                'description' => $data['description'] ?? null,
                'grand_total' => $data['grand_total'],
                'employee_id' => $data['employee_id'] ?? null,
                'created_by' => $data['created_by'],
            ]);

            if (!empty($data['products'])) {
                $products = [];
                foreach ($data['products'] as $product) {
                    $products[$product['id']] = [
                        'quantity' => $product['quantity'],
                        'base_price' => $product['base_price'],
                        'unit_price' => $product['unit_price'],
                        'total_price' => $product['quantity'] * $product['unit_price'],
                    ];

                    $this->updateProductPrices(
                        $product['id'],
                        $product['unit_price'],
                        $product['quantity']
                    );
                }
                $purchaseInvoice->products()->attach($products);
            }

            foreach ($data['products'] as $product) {
                app(StockMovementService::class)->recordPurchase(
                    $data['outlet_id'],
                    $product['id'],
                    $data['employee_id'],
                    $product['quantity']
                );
            }
        });

        return $purchaseInvoice;
    }

    // Update -> Void
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
            $purchaseInvoice->update([
                'outlet_id' => $data['outlet_id'], 
                'invoice_number' => $data['invoice_number'],
                'description' => $data['description'] ?? null,
                'grand_total' => $data['grand_total'],
                'employee_id' => $data['employee_id'] ?? null,
                'created_by' => $data['created_by'],
            ]);

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

    /**
     * Update product buy_price using weighted average cost method based on historical purchases
     * 
     * @param int $productId
     * @param float $unitPrice
     * @param int $newQuantity
     * @return void
     */
    private function updateProductPrices($productId, $unitPrice, $newQuantity)
    {
        $product = $this->productService->getProductById($productId);
        if (!$product) return;

        $previousPurchases = $this->getPreviousPurchasesForProduct($productId);

        $totalQuantity = 0;
        $totalAmount = 0;

        foreach ($previousPurchases as $purchaseInvoice) {
            $totalQuantity += $purchaseInvoice->quantity;
            $totalAmount += $purchaseInvoice->quantity * $purchaseInvoice->unit_price;
        }

        $totalQuantity += $newQuantity;
        $totalAmount += $newQuantity * $unitPrice;

        if ($totalQuantity > 0) {
            $weightedAverage = $totalAmount / $totalQuantity;
            $product->buy_price = $weightedAverage;
            $product->base_price = $weightedAverage * 1;
            $product->save();
        }
    }
}
