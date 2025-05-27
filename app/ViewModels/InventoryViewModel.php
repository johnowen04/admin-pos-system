<?php

namespace App\ViewModels;

use Illuminate\Support\Collection;

class InventoryViewModel
{
    protected ?Collection $mappedRows = null;

    public function __construct(
        protected Collection $products,
    ) {}

    /**
     * Get the inventory rows for display in the inventory table
     */
    public function rows(): Collection
    {
        if ($this->mappedRows) {
            return $this->mappedRows;
        }

        return $this->mappedRows = $this->products->map(function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'sku' => $row->sku,
                'category' => $row->category,
                'unit' => $row->unit,
                'initial' => $row->initial_quantity,
                'purchase' => $row->purchase,
                'sale' => $row->sale,
                'adjustment' => $row->adjustment,
                'balance' => $row->balance,
            ];
        });
    }
}