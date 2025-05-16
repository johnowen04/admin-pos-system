<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public $outletService;
    public $inventoryService;

    public function __construct(OutletService $outletService, InventoryService $inventoryService)
    {
        $this->outletService = $outletService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Get all products.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllProducts()
    {
        return Product::all();
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return \App\Models\Product
     */
    public function createProduct(array $data)
    {
        // Create the product
        $product = Product::create([
            'sku' => $data['sku'], // Explicitly set the SKU
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'base_price' => $data['base_price'] ?? 0,
            'buy_price' => $data['buy_price'],
            'sell_price' => $data['sell_price'],
            'min_qty' => $data['min_qty'],
            'is_shown' => $data['is_shown'],
            'categories_id' => $data['categories_id'],
            'units_id' => $data['units_id'],
        ]);

        // Attach outlets to the product (if any)
        if (!empty($data['outlets'])) {
            $this->inventoryService->initializeStockForNewProduct($data['outlets'], $product->id);
        }

        return $product;
    }

    /**
     * Update an existing product.
     *
     * @param \App\Models\Product $product
     * @param array $data
     * @return bool
     */
    public function updateProduct(Product $product, array $data)
    {
        // Update the product
        $product->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'base_price' => $data['base_price'] ?? 0,
            'buy_price' => $data['buy_price'],
            'sell_price' => $data['sell_price'],
            'min_qty' => $data['min_qty'],
            'is_shown' => $data['is_shown'],
            'categories_id' => $data['categories_id'],
            'units_id' => $data['units_id'],
        ]);

        // Sync outlets
        if (!empty($data['outlets'])) {
            $currentOutlets = $product->outlets()->pluck('outlet_id')->toArray();
            $newOutlets = $data['outlets']; // Assuming $data['outlets'] is [outlet_id => quantity]

            // Find removed and added outlets
            $removedOutlets = array_diff($currentOutlets, $newOutlets);
            $addedOutlets = array_diff($newOutlets, $currentOutlets);


            foreach ($removedOutlets as $_ => $outletId) {
                $inventory = $this->inventoryService->getStock($outletId, $product->id);
                if ($inventory > 0) {
                    $outletName = $this->outletService->getOutletById($outletId)->name ?? "Unknown Outlet";
                    throw ValidationException::withMessages([
                        'outlet' => "Cannot remove outlet '{$outletName}' because its inventory is not empty."
                    ]);
                }

                $this->inventoryService->deleteStock($outletId, $product->id);
            }

            $this->inventoryService->initializeStockForNewProduct($addedOutlets, $product->id);
        }

        return true;
    }

    /**
     * Delete a product.
     *
     * @param \App\Models\Product $product
     * @return bool|null
     */
    public function deleteProduct(Product $product)
    {
        return $product->delete();
    }

    /**
     * Get the selected outlets for a product.
     *
     * @param \App\Models\Product $product
     * @return array
     */
    public function getSelectedOutlets(Product $product)
    {
        return $product->outlets->pluck('id')->toArray();
    }

    /**
     * Get a product by SKU.
     *
     * @param string $sku
     * @return \App\Models\Product|null
     */
    public function getProductBySku(string $sku)
    {
        return Product::find($sku); // Use SKU as the primary key
    }

    /**
     * Get products by outlet ID.
     *
     * @param int $outletId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsByOutlets(array $outletIds)
    {
        return Product::whereHas('outlets', function ($query) use ($outletIds) {
            $query->where('outlets_id', $outletIds);
        })->get();
    }
}
