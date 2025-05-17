<?php

namespace App\Services;

use App\Models\Inventory;

class InventoryService
{
    public function getStocksByProduct(int $productId): array
    {
        return Inventory::where('product_id', $productId)
            ->with('outlet') // Eager load the related Outlet model
            ->get()
            ->map(function ($item) {
                return [
                    'outlet_id' => $item->outlet_id,
                    'outlet_name' => $item->outlet->name ?? 'Unknown Outlet', // Get outlet name or fallback
                    'quantity' => $item->quantity,
                ];
            })
            ->toArray();
    }

    public function getStocksByOutlet(int $outletId): array
    {
        return Inventory::where('outlet_id', $outletId)
            ->with('product') // Eager load the related Product model
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'sku' => $item->product->sku ?? 'XXXXXX', // Get SKU or fallback
                    'name' => $item->product->name ?? 'Unknown Product', // Get product name or fallback
                    'sell_price' => $item->product->sell_price ?? 0, // Get product sell price or fallback
                    'categories_id' => $item->product->categories_id ?? 0, // Get product category ID or fallback
                    'quantity' => $item->quantity,
                ];
            })
            ->toArray();
    }

    public function getStock(int $outletId, int $productId): int
    {
        return Inventory::where('outlet_id', $outletId)
            ->where('product_id', $productId)
            ->value('quantity') ?? 0;
    }

    public function setStock(int $outletId, int $productId, int $newQty): void
    {
        Inventory::updateOrCreate(
            ['outlet_id' => $outletId, 'product_id' => $productId],
            ['quantity' => $newQty]
        );
    }

    public function incrementStock(int $outletId, int $productId, int $amount): void
    {
        Inventory::where('outlet_id', $outletId)
            ->where('product_id', $productId)
            ->increment('quantity', $amount);
    }

    public function decrementStock(int $outletId, int $productId, int $amount): void
    {
        Inventory::where('outlet_id', $outletId)
            ->where('product_id', $productId)
            ->decrement('quantity', $amount);
    }

    public function initializeStockForNewProduct(array $outlets, int $productId)
    {
        foreach ($outlets as $_ => $outletId) {
            $this->setStock($outletId, $productId, 0);
        }
    }

    public function deleteStock(int $outletId, int $productId): void
    {
        Inventory::where('outlet_id', $outletId)
            ->where('product_id', $productId)
            ->forceDelete();
    }
}
