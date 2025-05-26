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

class DepartmentSalesReportExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $salesReportService;
    protected $report;
    protected $rows;
    protected $totalQuantity;
    protected $totalRevenue;
    protected $totalSales;
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
                'name' => $row->department_name,
                'sold_quantity' => $row->sold_quantity,
                'total_sold' => $row->total_sold,
                'percentage_qty' => round($row->percentage_qty, 2),
                'percentage_revenue' => round($row->percentage_revenue, 2),
            ];
        });

        $this->totalQuantity = $this->report->sum('sold_quantity');
        $this->totalSales = $this->rows->sum('total_sold');

        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Department Name',
            'Quantity Sold',
            'Percentage Qty (%)',
            'Total Sold',
            'Percentage Revenue (%)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->sold_quantity,
            $row->percentage_qty . '%',
            number_format($row->total_sold, 2),
            $row->percentage_revenue . '%',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Style the header row background color and font color for all columns (A to O)
                $sheet->getStyle('A9:E9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ffced8f2'); // green
                $sheet->getStyle('A9:E9')->getFont()->getColor()->setARGB('ff2a3e80'); // black font
                $sheet->getStyle('A9:E9')->getFont()->setBold(true);

                $sheet->setCellValue('E1', $this->createdBy);
                $sheet->setCellValue('E2', $this->selectedOutletName);

                $sheet->setCellValue('D4', "Total Penjualan");
                $sheet->setCellValue('E4', $this->totalSales);

                $sheet->setCellValue('D5', "Total Produk Terjual");
                $sheet->setCellValue('E5', $this->totalQuantity);

                $sheet->setCellValue('E7', "Date Generated " . now()->format('Y-m-d H:i:s'));

                // Apply number formatting (e.g., currency with thousands separator) on totals
                $sheet->getStyle('E4')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            },

            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert 4 rows above for title and date info
                $sheet->insertNewRowBefore(1, 7);

                // Merge and style the title row
                $sheet->mergeCells('A1:D1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // Set report title
                $sheet->setCellValue('A1', 'Laporan Penjualan Per Department');

                // Set date range below title
                $sheet->setCellValue('A2', "From: {$this->startDate}");
                $sheet->setCellValue('B2', "To: {$this->endDate}");
            },
        ];
    }
}
