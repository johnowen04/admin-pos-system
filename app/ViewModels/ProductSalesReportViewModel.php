<?php

namespace App\ViewModels;

use Illuminate\Support\Collection;

class ProductSalesReportViewModel
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
        $this->totalRevenue = $this->report->sum(fn ($row) => $row->sold_quantity * $row->unit_price);

        return $this->mappedRows = $this->report->map(function ($row) {
            $totalSold = $row->sold_quantity * $row->unit_price;
            $totalRefund = $row->refund_quantity * $row->unit_price;
            $totalSoldCogs = $row->sold_quantity * $row->base_price;
            $totalRefundCogs = $row->refund_quantity * $row->base_price;
            $grossProfit = ($totalSold - $totalSoldCogs) - $totalRefund + $totalRefundCogs;

            return [
                'name' => $row->product_name,
                'sku' => $row->product_sku,
                'outlet' => $row->outlet_name,
                'category' => $row->product_category,
                'base_price' => $row->base_price,
                'unit_price' => $row->unit_price,
                'sold_quantity' => $row->sold_quantity,
                'total_sold' => $totalSold,
                'refund_quantity' => $row->refund_quantity,
                'total_refund' => $totalRefund,
                'percentage_qty' => $this->totalQuantity > 0
                    ? round(($row->sold_quantity / $this->totalQuantity) * 100, 2)
                    : 0,
                    'percentage_revenue' => $this->totalRevenue > 0
                        ? round(($totalSold / $this->totalRevenue) * 100, 2)
                        : 0,
                'total_sold_cogs' => $totalSoldCogs,
                'total_refund_cogs' => $totalRefundCogs,
                'gross_profit' => $grossProfit,
            ];
        });
    }

    public function totalGrossProfit(): float
    {
        return $this->rows()->sum('gross_profit');
    }

    public function totalRevenue(): float
    {
        return $this->totalRevenue ??= $this->rows()->sum('total_sold');
    }

    public function totalRefund(): float
    {
        return $this->rows()->sum('total_refund');
    }

    public function totalQuantity(): float
    {
        return $this->totalQuantity ??= $this->rows()->sum('sold_quantity');
    }
}
