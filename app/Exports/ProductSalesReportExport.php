<?php

namespace App\Exports;

use App\Services\AccessControlService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductSalesReportExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $salesReportService;
    protected $report;
    protected $rows;
    protected $totalQuantity;
    protected $totalRevenue;
    protected $totalSales;
    protected $grossProfit;
    protected $selectedOutletName;
    protected $createdBy;

    public function __construct($report, $startDate, $endDate)
    {
        $this->report = $report;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->selectedOutletName = session('selected_outlet', 'All Outlets');
        $this->createdBy = app(AccessControlService::class)->getUser()->name;
    }

    public function collection()
    {
        $this->totalQuantity = $this->report->sum('sold_quantity');
        $this->totalRevenue = $this->report->sum(fn($row) => $row->sold_quantity * $row->unit_price);

        $this->rows = $this->report->map(function ($row) {
            $totalSold = $row->sold_quantity * $row->unit_price;
            $totalRefund = $row->refund_quantity * $row->unit_price;
            $totalSoldCogs = $row->sold_quantity * $row->base_price;
            $totalRefundCogs = $row->refund_quantity * $row->base_price;
            $grossProfit = ($totalSold - $totalSoldCogs) - $totalRefund + $totalRefundCogs;

            return (object) [
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
                'percentage_qty' => $this->totalQuantity > 0 ? round(($row->sold_quantity / $this->totalQuantity) * 100, 2) : 0,
                'percentage_revenue' => $this->totalRevenue > 0 ? round(($totalSold / $this->totalRevenue) * 100, 2) : 0,
                'total_sold_cogs' => $totalSoldCogs,
                'total_refund_cogs' => $totalRefundCogs,
                'gross_profit' => $grossProfit,
            ];
        });

        $this->totalSales = $this->rows->sum('total_sold');
        $this->grossProfit = $this->rows->sum('gross_profit');

        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Product SKU',
            'Outlet',
            'Product Category',
            'Base Price',
            'Unit Price',
            'Quantity Sold',
            'Total Sold',
            'Refund Quantity',
            'Total Refund',
            'Percentage Qty (%)',
            'Percentage Revenue (%)',
            'Total Sold COGS',
            'Total Refund COGS',
            'Gross Profit',
        ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->sku,
            $row->outlet,
            $row->category,
            number_format($row->base_price, 2),
            number_format($row->unit_price, 2),
            $row->sold_quantity,
            number_format($row->total_sold, 2),
            $row->refund_quantity,
            number_format($row->total_refund, 2),
            $row->percentage_qty . '%',
            $row->percentage_revenue . '%',
            number_format($row->total_sold_cogs, 2),
            number_format($row->total_refund_cogs, 2),
            number_format($row->gross_profit, 2),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Style the header row background color and font color for all columns (A to O)
                $sheet->getStyle('A10:O10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4CAF50'); // green
                $sheet->getStyle('A10:O10')->getFont()->getColor()->setARGB('FFFFFFFF'); // white font
                $sheet->getStyle('A10:O10')->getFont()->setBold(true);

                $sheet->setCellValue('O1', $this->createdBy);
                $sheet->setCellValue('O2', $this->selectedOutletName);

                $sheet->setCellValue('N4', "Total Penjualan");
                $sheet->setCellValue('O4', $this->totalSales);

                $sheet->setCellValue('N5', "Total Laba Kotor");
                $sheet->setCellValue('O5', $this->grossProfit);

                $sheet->setCellValue('N6', "Total Produk Terjual");
                $sheet->setCellValue('O6', $this->totalQuantity);
                
                $sheet->setCellValue('O8', "Date Generated ". now()->format('Y-m-d H:i:s'));

                // Apply number formatting (e.g., currency with thousands separator) on totals
                $sheet->getStyle('O4')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle('O5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            },

            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert 4 rows above for title and date info
                $sheet->insertNewRowBefore(1, 8);

                // Merge and style the title row
                $sheet->mergeCells('A1:N1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // Set report title
                $sheet->setCellValue('A1', 'Laporan Penjualan Produk');

                // Set date range below title
                $sheet->setCellValue('A2', "From: {$this->startDate}");
                $sheet->setCellValue('B2', "To: {$this->endDate}");
            },
        ];
    }
}
