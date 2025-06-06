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
        $this->rows = $this->report->map(function ($row) {
            return (object) [
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

        $this->totalQuantity = $this->report->sum('sold_quantity');
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
            'Quantity Sold',
            'Percentage Qty (%)',
            'Total Sold',
            'Percentage Revenue (%)',
            'Total Sold COGS',
            'Refund Quantity',
            'Total Refund',
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
            $row->sold_quantity,
            $row->percentage_qty . '%',
            number_format($row->total_sold, 2),
            $row->percentage_revenue . '%',
            number_format($row->total_sold_cogs, 2),
            $row->refund_quantity,
            number_format($row->total_refund, 2),
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
                $sheet->getStyle('A10:M10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ffced8f2'); // green
                $sheet->getStyle('A10:M10')->getFont()->getColor()->setARGB('ff2a3e80'); // black font
                $sheet->getStyle('A10:M10')->getFont()->setBold(true);

                $sheet->setCellValue('M1', $this->createdBy);
                $sheet->setCellValue('M2', $this->selectedOutletName);

                $sheet->setCellValue('L4', "Total Penjualan");
                $sheet->setCellValue('M4', $this->totalSales);

                $sheet->setCellValue('L5', "Total Laba Kotor");
                $sheet->setCellValue('M5', $this->grossProfit);

                $sheet->setCellValue('L6', "Total Produk Terjual");
                $sheet->setCellValue('M6', $this->totalQuantity);

                $sheet->setCellValue('M8', "Date Generated " . now()->format('Y-m-d H:i:s'));

                // Apply number formatting (e.g., currency with thousands separator) on totals
                $sheet->getStyle('M4')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle('M5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            },

            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert 4 rows above for title and date info
                $sheet->insertNewRowBefore(1, 8);

                // Merge and style the title row
                $sheet->mergeCells('A1:L1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // Set report title
                $sheet->setCellValue('A1', 'Laporan Penjualan Per Produk');

                // Set date range below title
                $sheet->setCellValue('A2', "From: {$this->startDate}");
                $sheet->setCellValue('B2', "To: {$this->endDate}");
            },
        ];
    }
}
