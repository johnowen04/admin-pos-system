<?php

namespace App\ViewModels;

use Illuminate\Support\Collection;

class DepartmentSalesReportViewModel
{
    public function __construct(protected Collection $report) {}

    protected ?Collection $mappedRows = null;
    protected ?float $totalQuantity = null;
    protected ?float $totalRevenue = null;

    public function rows(): Collection
    {
        if ($this->mappedRows) {
            return $this->mappedRows;
        }

        $this->totalQuantity = $this->report->sum('sold_quantity');
        $this->totalRevenue = $this->report->sum('total_sold');

        return $this->mappedRows = $this->report->map(function ($row) {

            return [
                'name' => $row->product_department,
                'sold_quantity' => $row->sold_quantity,
                'total_sold' => $row->total_sold,
                'percentage_qty' => $this->totalQuantity > 0
                    ? round(($row->sold_quantity / $this->totalQuantity) * 100, 2)
                    : 0,
                'percentage_revenue' => $this->totalRevenue > 0
                    ? round(($row->total_sold / $this->totalRevenue) * 100, 2)
                    : 0,
            ];
        });
    }

    public function totalRevenue(): float
    {
        return $this->totalRevenue ??= $this->rows()->sum('total_sold');
    }

    public function totalQuantity(): float
    {
        return $this->totalQuantity ??= $this->rows()->sum('sold_quantity');
    }
}
