<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Services\InventoryService;
use App\Enums\StockMovementType;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function recordInitialStock(int $outletId, int $productId, ?int $employeeId, int $quantity, ?string $reason = null): StockMovement
    {
        return $this->createMovement($outletId, $productId, $employeeId, StockMovementType::INITIAL, $quantity, $reason);
    }

    public function recordPurchase(int $outletId, int $productId, ?int $employeeId, int $quantity, ?string $reason = null): StockMovement
    {
        return $this->createMovement($outletId, $productId, $employeeId, StockMovementType::PURCHASE, $quantity, $reason);
    }

    public function recordSale(int $outletId, int $productId, ?int $employeeId, int $quantity, ?string $reason = null): StockMovement
    {
        return $this->createMovement($outletId, $productId, $employeeId, StockMovementType::SALE, $quantity, $reason);
    }

    public function recordAdjustment(int $outletId, int $productId, ?int $employeeId, int $quantity, ?string $reason = null): StockMovement
    {
        return $this->createMovement($outletId, $productId, $employeeId, StockMovementType::ADJUSTMENT, $quantity, $reason);
    }

    private function createMovement(
        int $outletId,
        int $productId,
        ?int $employeeId,
        StockMovementType $movementType,
        int $quantity,
        ?string $reason = null
    ): StockMovement {
        return DB::transaction(function () use ($outletId, $productId, $employeeId, $movementType, $quantity, $reason) {
            match ($movementType) {
                StockMovementType::INITIAL,
                StockMovementType::PURCHASE => $this->inventoryService->incrementStock($outletId, $productId, $quantity),

                StockMovementType::SALE => $this->inventoryService->decrementStock($outletId, $productId, $quantity),

                StockMovementType::ADJUSTMENT => $this->handleAdjustment($outletId, $productId, $quantity),
            };

            return StockMovement::create([
                'outlet_id'     => $outletId,
                'product_id'    => $productId,
                'employee_id'   => $employeeId,
                'movement_type' => $movementType->value,
                'quantity'      => $quantity,
                'reason'        => $reason,
            ]);
        });
    }

    private function handleAdjustment(int $outletId, int $productId, int $quantity): void
    {
        if ($quantity > 0) {
            $this->inventoryService->incrementStock($outletId, $productId, $quantity);
        } elseif ($quantity < 0) {
            $this->inventoryService->decrementStock($outletId, $productId, abs($quantity));
        }
    }
}
