<?php

namespace App\ViewModels;

use Illuminate\Support\Collection;

class ProductSalesReportViewModel
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
                'name' => $row->product_name,
                'sku' => $row->product_sku,
                'outlet' => $row->outlet_name,
                'category' => $row->category_name,
                'sold_quantity' => $row->sold_quantity,
                'total_sold' => $row->total_sold,
                'refund_quantity' => $row->refund_quantity,
                'total_refund' => $row->total_refund,
                'percentage_qty' => round($row->percentage_qty, 2),
                'percentage_revenue' => round($row->percentage_revenue, 2),
                'total_sold_cogs' => $row->total_sold_cogs,
                'total_refund_cogs' => $row->total_refund_cogs,
                'gross_profit' => $row->gross_profit,
            ];
        });
    }

    public function totalGrossProfit(): float
    {
        return $this->rows()->sum('gross_profit');
    }

    public function totalRevenue(): float
    {
        return $this->rows()->sum('total_sold');
    }

    public function totalRefund(): float
    {
        return $this->rows()->sum('total_refund');
    }

    public function totalQuantity(): float
    {
        return $this->rows()->sum('sold_quantity');
    }
}
