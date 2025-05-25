<?php

namespace App\Http\Controllers\Reports;

use App\Exports\ProductSalesReportExport;
use App\Http\Controllers\Controller;
use App\Services\AccessControlService;
use App\Services\Reports\SalesReportService;
use App\ViewModels\ProductSalesReportViewModel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SalesReportController extends Controller
{
    public function productReport(Request $request)
    {
        $data = $this->getProductSalesReportData($request);
        $viewModel = new ProductSalesReportViewModel($data);

        return view('reports.sales.product', ['report' => $viewModel]);
    }

    /**
     * Export sales report to Excel.
     */
    public function exportProductSalesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start = $request->input('start_date') ?: now();
        $end = $request->input('end_date') ?: now();

        $data = $this->getProductSalesReportData($request);

        return Excel::download(new ProductSalesReportExport($data, $start, $end), 'product_sales_report_' . $start . '.xlsx');
    }

    /**
     * Get product sales report data with outlet filter and date range.
     */
    private function getProductSalesReportData(Request $request)
    {
        $start = $request->input('start_date') ?: now();
        $end = $request->input('end_date') ?: now();

        $selectedOutletId = session('selected_outlet_id', null);

        $salesReportService = app(SalesReportService::class);

        if (!$selectedOutletId || $selectedOutletId === 'all') {
            return $salesReportService->getProductSalesReportData($start, $end);
        }

        return $salesReportService->getProductSalesReportData($start, $end, $selectedOutletId);
    }
}
