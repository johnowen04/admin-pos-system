<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportService
{
    public function getProductSalesReportData($startDate, $endDate, ?int $outletId = null)
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $query = DB::table('sales_invoice_product as sip')
            ->join('sales_invoices as si', 'si.id', '=', 'sip.sales_invoice_id')
            ->join('products as p', 'p.id', '=', 'sip.product_id')
            ->join('categories as c', 'p.categories_id', '=', 'c.id')
            ->join('outlets as o', 'si.outlet_id', '=', 'o.id')
            ->whereBetween('si.created_at', [$startDate, $endDate]);

        if ($outletId !== null) {
            $query->where('si.outlet_id', $outletId);
        }

        return $query
            ->select([
                'p.name as product_name',
                'p.sku as product_sku',
                'o.name as outlet_name',
                'c.name as product_category',
                'sip.base_price',
                'sip.unit_price',
                DB::raw('SUM(sip.quantity) as sold_quantity'),
                DB::raw('SUM(CASE WHEN si.is_voided = 1 THEN sip.quantity ELSE 0 END) as refund_quantity'),
            ])
            ->groupBy(
                'product_name',
                'product_sku',
                'outlet_name',
                'product_category',
                'sip.base_price',
                'sip.unit_price'
            )
            ->orderBy('si.created_at')
            ->get();
    }

    public function getCategorySalesReportData($startDate, $endDate, ?int $outletId = null)
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $query = DB::table('sales_invoice_product as sip')
            ->join('sales_invoices as si', 'si.id', '=', 'sip.sales_invoice_id')
            ->join('products as p', 'p.id', '=', 'sip.product_id')
            ->join('categories as c', 'p.categories_id', '=', 'c.id')
            ->join('outlets as o', 'si.outlet_id', '=', 'o.id')
            ->whereBetween('si.created_at', [$startDate, $endDate]);

        if ($outletId !== null) {
            $query->where('si.outlet_id', $outletId);
        }

        return $query
            ->select([
                'c.name as product_category',
                DB::raw('SUM(sip.quantity) as sold_quantity'),
                DB::raw('SUM(sip.unit_price * sip.quantity) as total_sold'),
                DB::raw('SUM(sip.base_price * sip.quantity) as total_sold_cogs'),
            ])
            ->groupBy('product_category')
            ->orderBy('si.created_at')
            ->get();
    }

    public function getDepartmentSalesReportData($startDate, $endDate, ?int $outletId = null)
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $query = DB::table('sales_invoice_product as sip')
            ->join('sales_invoices as si', 'si.id', '=', 'sip.sales_invoice_id')
            ->join('products as p', 'p.id', '=', 'sip.product_id')
            ->join('categories as c', 'p.categories_id', '=', 'c.id')
            ->join('departments as d', 'c.department_id', '=', 'd.id')
            ->join('outlets as o', 'si.outlet_id', '=', 'o.id')
            ->whereBetween('si.created_at', [$startDate, $endDate]);

        if ($outletId !== null) {
            $query->where('si.outlet_id', $outletId);
        }

        return $query
            ->select([
                'd.name as product_department',
                DB::raw('SUM(sip.unit_price * sip.quantity) as total_sold'),
                DB::raw('SUM(sip.quantity) as sold_quantity'),
            ])
            ->groupBy('product_department')
            ->orderBy('si.created_at')
            ->get();
    }
}
