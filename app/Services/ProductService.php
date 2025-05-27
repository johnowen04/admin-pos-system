<?php

namespace App\Services;

use App\Models\Outlet;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected StockMovementService $stockMovementService
    ) {}

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
     * Get a product by ID.
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function getProductById(int $id)
    {
        return Product::find($id);
    }

    /**
     * Get products by IDs.
     *
     * @param array $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsByIds(array $ids)
    {
        return Product::whereIn('id', $ids)->get();
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
     * Create a new product.
     */
    public function createProduct(array $data): Product
    {
        $product = Product::create($this->extractProductAttributes($data));

        if (!empty($data['outlets'])) {
            $this->initializeOutletsForProduct($product, $data['outlets']);
        }

        return $product;
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(Product $product, array $data): bool
    {
        $product->update($this->extractProductAttributes($data, updating: true));

        if (!empty($data['outlets'])) {
            $this->syncProductOutlets($product, $data['outlets']);
        }

        return true;
    }

    /**
     * Extract product attributes from request data.
     */
    private function extractProductAttributes(array $data, bool $updating = false): array
    {
        return [
            'sku' => $data['sku'] ?? ($updating ?: null),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'base_price' => $data['base_price'] ?? 0,
            'buy_price' => $data['buy_price'] ?? 0,
            'sell_price' => $data['sell_price'],
            'min_qty' => $data['min_qty'],
            'is_shown' => $data['is_shown'],
            'category_id' => $data['category_id'],
            'unit_id' => $data['unit_id'],
        ];
    }

    /**
     * Initialize inventory and stock movement for a product.
     */
    private function initializeOutletsForProduct(Product $product, array $outlets): void
    {
        $employeeId = Auth::id();

        $this->inventoryService->initializeStockForNewProduct($outlets, $product->id);

        foreach ($outlets as $outletId) {
            $this->stockMovementService->recordInitialStock(
                $outletId,
                $product->id,
                $employeeId,
                0
            );
        }
    }

    /**
     * Sync product outlets.
     */
    private function syncProductOutlets(Product $product, array $outlets): void
    {
        $currentOutlets = $product->outlets()->pluck('outlet_id')->toArray();

        $removed = array_diff($currentOutlets, $outlets);
        $added = array_diff($outlets, $currentOutlets);

        $this->handleRemovedOutlets($product, $removed);
        $this->initializeOutletsForProduct($product, $added);
    }

    /**
     * Handle removed outlets and clean inventory.
     */
    private function handleRemovedOutlets(Product $product, array $removed): void
    {
        foreach ($removed as $outletId) {
            $inventory = $this->inventoryService->getStock($outletId, $product->id);

            if ($inventory > 0) {
                $outletName = Outlet::find($outletId)->name ?? "Unknown Outlet";

                throw ValidationException::withMessages([
                    'outlet' => "Cannot remove outlet '{$outletName}' because its inventory is not empty."
                ]);
            }

            $this->inventoryService->deleteStock($outletId, $product->id);
        }
    }

    /**
     * Delete a product.
     */
    public function deleteProduct(Product $product): ?bool
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
}
