<?php

namespace App\Http\Controllers\Reports;

use App\Exports\CategorySalesReportExport;
use App\Exports\DepartmentSalesReportExport;
use App\Exports\ProductSalesReportExport;
use App\Http\Controllers\Controller;
use App\Services\Reports\SalesReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SalesReportController extends Controller
{
    public function products()
    {
        $selectedOutletId = session('selected_outlet_id', null);
        return view('reports.sales.index', [
            'reportType' => 'Product',
            'selectedOutletId' => $selectedOutletId,
            'exportRoute' => route('reports.sales.products.export'),
        ]);
    }

    /**
     * Export sales report to Excel.
     */
    public function productsExport(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $start = $validated['start_date'];
        $end = $validated['end_date'];

        $data = $this->getProductSalesReportData($start, $end);

        return Excel::download(
            new ProductSalesReportExport($data, $start, $end),
            "product_sales_report_{$start}.xlsx"
        );
    }

    /**
     * Get product sales report data based on outlet filter and date range.
     */
    private function getProductSalesReportData(string $start, string $end)
    {
        $selectedOutletId = session('selected_outlet_id');
        $query = app(SalesReportService::class)->getProductSalesReportQuery($start, $end, $selectedOutletId);
        return $query->orderBy('sip.created_at', 'asc')->get();
    }

    public function categories()
    {
        $selectedOutletId = session('selected_outlet_id', null);
        return view('reports.sales.index', [
            'reportType' => 'Category',
            'selectedOutletId' => $selectedOutletId,
            'exportRoute' => route('reports.sales.categories.export'),
        ]);
    }

    /**
     * Export sales report to Excel.
     */
    public function categoriesExport(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $start = $validated['start_date'];
        $end = $validated['end_date'];

        $data = $this->getCategorySalesReportData($start, $end);

        return Excel::download(
            new CategorySalesReportExport($data, $start, $end),
            "category_sales_report_{$start}.xlsx"
        );
    }

    /**
     * Get category sales report data with outlet filter and date range.
     */
    private function getCategorySalesReportData(string $start, string $end)
    {
        $selectedOutletId = session('selected_outlet_id');
        $query = app(SalesReportService::class)->getCategorySalesReportQuery($start, $end, $selectedOutletId);
        return $query->orderBy('sip.created_at', 'asc')->get();
    }

    public function departments()
    {
        $selectedOutletId = session('selected_outlet_id', null);
        return view('reports.sales.index', [
            'reportType' => 'Department',
            'selectedOutletId' => $selectedOutletId,
            'exportRoute' => route('reports.sales.departments.export'),
        ]);
    }

    /**
     * Export sales report to Excel.
     */
    public function departmentsExport(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $start = $validated['start_date'];
        $end = $validated['end_date'];

        $data = $this->getDepartmentSalesReportData($start, $end);

        return Excel::download(
            new DepartmentSalesReportExport($data, $start, $end),
            "department_sales_report_{$start}.xlsx"
        );
    }

    /**
     * Get department sales report data with outlet filter and date range.
     */
    private function getDepartmentSalesReportData(string $start, string $end)
    {
        $selectedOutletId = session('selected_outlet_id');
        $query = app(SalesReportService::class)->getDepartmentSalesReportQuery($start, $end, $selectedOutletId);
        return $query->orderBy('sip.created_at', 'asc')->get();
    }
}
