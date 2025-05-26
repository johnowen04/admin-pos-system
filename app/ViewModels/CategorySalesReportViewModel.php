<?php

namespace App\ViewModels;

use Illuminate\Support\Collection;

class CategorySalesReportViewModel
{
    public function __construct(protected Collection $report) {}

    protected ?Collection $mappedRows = null;

    public function rows(): Collection
    {
        if ($this->mappedRows) {
            return $this->mappedRows;
        }

        return $this->mappedRows = $this->report->map(function ($row) {
            return [
                'name' => $row->category_name,
                'sold_quantity' => $row->sold_quantity,
                'total_sold' => $row->total_sold,
                'percentage_qty' => round($row->percentage_qty, 2),
                'percentage_revenue' => round($row->percentage_revenue, 2),
                'total_sold_cogs' => $row->total_sold_cogs,
            ];
        });
    }

    public function totalRevenue(): float
    {
        return $this->rows()->sum('total_sold');
    }

    public function totalQuantity(): float
    {
        return $this->rows()->sum('sold_quantity');
    }
}
