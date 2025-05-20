<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Services\Reports\SalesReportService;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $salesReportService;
    protected $rows;
    protected $totalJual;
    protected $totalLabaKotor;

    public function __construct($startDate, $endDate)
    {
        $this->salesReportService = new SalesReportService();
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $this->rows = $this->salesReportService->getProductSalesReportData($this->startDate, $this->endDate);

        $this->totalJual = $this->rows->sum('total_jual');
        $this->totalLabaKotor = $this->rows->sum('laba_kotor');

        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Product SKU',
            'Outlet',
            'Product Category',
            'Jumlah Terjual',
            'Harga Jual',
            'Total Penjualan',
            'HPP',
            'Total HPP',
            'Laba Kotor',
        ];
    }

    public function map($row): array
    {
        return [
            $row->product_name,
            $row->product_sku,
            $row->outlet_name,
            $row->product_category,
            $row->total_quantity,
            number_format($row->unit_price, 2),
            number_format($row->total_jual, 2),
            number_format($row->base_price, 2),
            number_format($row->total_hpp, 2),
            number_format($row->laba_kotor, 2),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Style the header row background color and font color
                $sheet->getStyle('A6:J6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4CAF50'); // green

                $sheet->getStyle('A6:J6')->getFont()->getColor()->setARGB('FFFFFFFF'); // white font

                // Optional: Make headers bold
                $sheet->getStyle('A6:J6')->getFont()->setBold(true);

                $sheet->setCellValue('I2', "Total Penjualan");
                $sheet->setCellValue('J2', $this->totalJual);

                $sheet->setCellValue('I3', "Total Laba Kotor");
                $sheet->setCellValue('J3', $this->totalLabaKotor);

                // Apply number formatting (e.g., currency with thousands separator)
                $sheet->getStyle('J2')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle('J3')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            },

            BeforeSheet::class => function (BeforeSheet $event) {
                // Insert custom title rows above the data starting at row 1
                $sheet = $event->sheet->getDelegate();

                // Shift the existing data down by 4 rows (title + date + blank row)
                $sheet->insertNewRowBefore(1, 4);

                // Optionally, merge cells for the title row for better visual
                $sheet->mergeCells('A1:J1');
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
