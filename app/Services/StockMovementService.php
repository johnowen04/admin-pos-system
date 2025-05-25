<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Services\InventoryService;
use App\Enums\StockMovementType;
use App\Contracts\ReversibleInvoice;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function getProductsWithMovements(?int $outletId = null)
    {
        $products = app(ProductService::class)->getAllProducts();

        foreach ($products as $product) {
            $product->movements = $this->getAllMovements($product->id, $outletId);
        }

        return $products;
    }

    public function getMovementsByProduct(int $productId)
    {
        return $this->getAllMovements(productId: $productId);
    }

    public function getMovementsByOutlet(int $outletId)
    {
        return $this->getAllMovements(outletId: $outletId);
    }

    public function getAllMovements(?int $productId = null, ?int $outletId = null)
    {
        return StockMovement::query()
            ->when($outletId, fn($query) => $query->where('outlet_id', $outletId))
            ->when($productId, fn($query) => $query->where('product_id', $productId))
            ->orderByDesc('created_at')
            ->get();
    }

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

    public function reverseInvoice(ReversibleInvoice $invoice, int $employeeId, string $reason): void
    {
        foreach ($invoice->products as $product) {
            $reversedQty = $invoice->reversedQuantityFor($product->pivot);
            $this->recordAdjustment($invoice->outlet_id, $product->pivot->product_id, $employeeId, $reversedQty, 'Void invoice #' . $invoice->invoice_number . ': ' . $reason);
        }
    }
}
