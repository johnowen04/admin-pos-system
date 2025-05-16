<?php

namespace App\Services;

use App\Models\Inventory;

class InventoryService
{
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
