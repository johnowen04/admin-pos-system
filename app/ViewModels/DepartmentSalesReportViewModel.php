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

        return $this->mappedRows = $this->report->map(function ($row) {
            return [
                'name' => $row->department_name,
                'sold_quantity' => $row->sold_quantity,
                'total_sold' => $row->total_sold,
                'percentage_qty' => round($row->percentage_qty, 2),
                'percentage_revenue' => round($row->percentage_revenue, 2),
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
