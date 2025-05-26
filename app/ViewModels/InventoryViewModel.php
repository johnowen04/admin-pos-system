<?php

namespace App\ViewModels;

use Illuminate\Support\Collection;

class InventoryViewModel
{
    protected ?Collection $mappedRows = null;

    public function __construct(
        protected Collection $products,
        protected string $filter = 'non-zero'
    ) {}

    public function rows(): Collection
    {
        if ($this->mappedRows) {
            return $this->mappedRows;
        }

        $this->mappedRows = $this->products->map(function ($product) {
            $movementGroups = [];
            $detailStock = [];

            foreach ($product->stockMovements as $movement) {
                $outletId = $movement->outlet_id ?? 'unknown';

                $movementType = $movement->movement_type->value ?? 'unknown';
                $quantity = $movement->quantity;

                // Grouped for summary
                $movementGroups[$movementType][$outletId][] = [
                    'quantity' => $quantity,
                ];

                // Grouped for modal detail
                $outletName = $movement->outlet->name ?? 'Unknown Outlet';
                $unit = $product->unit->name ?? 'pcs';

                if (!isset($detailStock[$outletId])) {
                    $detailStock[$outletId] = [
                        'name' => $outletName,
                        'initial' => 0,
                        'purchase' => 0,
                        'sale' => 0,
                        'return' => 0,
                        'refund' => 0,
                        'adjustment' => 0,
                        'unit' => $unit,
                    ];
                }

                if ($movementType === 'adjustment') {
                    $detailStock[$outletId]['adjustment'] += $quantity;
                    if ($quantity < 0) {
                        $detailStock[$outletId]['return'] += abs($quantity);
                    } elseif ($quantity > 0) {
                        $detailStock[$outletId]['refund'] += $quantity;
                    }
                } else {
                    $detailStock[$outletId][$movementType] += $quantity;
                }
            }

            $sumQuantity = fn(?array $group) =>
            array_sum(array_map(fn($items) => array_sum(array_column($items, 'quantity')), $group ?? []));

            $initial = $product->initial_quantity ?? 0;
            $purchase = $sumQuantity($movementGroups['purchase'] ?? []);
            $sale = $sumQuantity($movementGroups['sale'] ?? []);
            $adjustment = $sumQuantity($movementGroups['adjustment'] ?? []);
            $balance = $initial + $purchase - $sale + $adjustment;

            return [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category->name ?? 'Uncategorized',
                'unit' => $product->unit->name ?? 'pcs',
                'initial' => $initial,
                'purchase' => $purchase,
                'sale' => $sale,
                'adjustment' => $adjustment,
                'balance' => $balance,
                'stock' => $detailStock,
            ];
        })->filter(function ($row) {
            if ($this->filter === 'all') {
                return true;
            }

            return $this->isNonZero($row['initial'])
                || $this->isNonZero($row['purchase'])
                || $this->isNonZero($row['sale'])
                || $this->isNonZero($row['adjustment']);
        })->values();

        return $this->mappedRows;
    }

    private function isNonZero(float|string|null $value): bool
    {
        return (float) $value !== 0.0;
    }
}
