<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportService
{
    private function getTotalSoldAndRevenue($startDate = null, $endDate = null)
    {
        $query = DB::table('sales_invoice_products as sip')
            ->join('sales_invoices as si', 'si.id', '=', 'sip.sales_invoice_id')
            ->where('si.is_voided', 0);

        if ($startDate !== null && $endDate !== null) {
            $query->whereBetween('si.created_at', [$startDate, $endDate]);
        }

        $totalSoldQuantity = (clone $query)->sum('sip.quantity');

        $totalRevenue = (clone $query)->sum(DB::raw('sip.unit_price * sip.quantity'));

        return [$totalSoldQuantity, $totalRevenue];
    }

    public function getProductSalesReportQuery($startDate = null, $endDate = null, $outletId = null)
    {
        if ($startDate !== null && $endDate !== null) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            [$totalSoldQuantity, $totalRevenue] = $this->getTotalSoldAndRevenue($startDate, $endDate);
        } else {
            $startDate = null;
            $endDate = null;
            [$totalSoldQuantity, $totalRevenue] = $this->getTotalSoldAndRevenue();
        }

        $query = DB::table('sales_invoice_products as sip')
            ->join('sales_invoices as si', 'si.id', '=', 'sip.sales_invoice_id')
            ->join('products as p', 'p.id', '=', 'sip.product_id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->join('outlets as o', 'si.outlet_id', '=', 'o.id');

        if ($startDate !== null && $endDate !== null) {
            $query->whereBetween('si.created_at', [$startDate, $endDate]);
        }

        if ($outletId !== null) {
            $query->where('si.outlet_id', $outletId);
        }

        return $query
            ->select([
                'p.name as product_name',
                'p.sku as product_sku',
                'o.name as outlet_name',
                'c.name as category_name',
                DB::raw('SUM(sip.quantity) as sold_quantity'),
                DB::raw('SUM(sip.quantity * sip.unit_price) as total_sold'),
                DB::raw('SUM(CASE WHEN si.is_voided = 1 THEN sip.quantity ELSE 0 END) as refund_quantity'),
                DB::raw('SUM(CASE WHEN si.is_voided = 1 THEN sip.quantity ELSE 0 END * sip.unit_price) as total_refund'),
                DB::raw('SUM(sip.quantity) / ' . ($totalSoldQuantity ?: 1) . ' * 100 as percentage_qty'),
                DB::raw('SUM(sip.quantity * sip.unit_price) / ' . ($totalRevenue ?: 1) . ' * 100 as percentage_revenue'),
                DB::raw('SUM(sip.quantity * sip.base_price) as total_sold_cogs'),
                DB::raw('SUM(CASE WHEN si.is_voided = 1 THEN sip.quantity ELSE 0 END * sip.base_price) as total_refund_cogs'),
                DB::raw('(SUM(sip.quantity * sip.unit_price) - SUM(sip.quantity * sip.base_price)) - SUM(CASE WHEN si.is_voided = 1 THEN sip.quantity ELSE 0 END * sip.base_price) as gross_profit'),
            ])
            ->groupBy(
                'p.name',
                'p.sku',
                'o.name',
                'c.name',
            );
    }

    public function getCategorySalesReportQuery($startDate = null, $endDate = null, $outletId = null)
    {
        if ($startDate !== null && $endDate !== null) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            [$totalSoldQuantity, $totalRevenue] = $this->getTotalSoldAndRevenue($startDate, $endDate);
        } else {
            $startDate = null;
            $endDate = null;
            [$totalSoldQuantity, $totalRevenue] = $this->getTotalSoldAndRevenue();
        }

        $query = DB::table('sales_invoice_products as sip')
            ->join('sales_invoices as si', 'si.id', '=', 'sip.sales_invoice_id')
            ->join('products as p', 'p.id', '=', 'sip.product_id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->join('outlets as o', 'si.outlet_id', '=', 'o.id');

        if ($startDate !== null && $endDate !== null) {
            $query->whereBetween('si.created_at', [$startDate, $endDate]);
        }

        if ($outletId !== null) {
            $query->where('si.outlet_id', $outletId);
        }

        return $query
            ->select([
                'c.name as category_name',
                DB::raw('SUM(sip.quantity) as sold_quantity'),
                DB::raw('SUM(sip.quantity * sip.unit_price) as total_sold'),
                DB::raw('SUM(sip.quantity) / ' . ($totalSoldQuantity ?: 1) . ' * 100 as percentage_qty'),
                DB::raw('SUM(sip.quantity * sip.unit_price) / ' . ($totalRevenue ?: 1) . ' * 100 as percentage_revenue'),
                DB::raw('SUM(sip.quantity * sip.base_price) as total_sold_cogs'),
            ])
            ->groupBy(
                'c.name',
            );
    }

    public function getDepartmentSalesReportQuery($startDate = null, $endDate = null, $outletId = null)
    {
        if ($startDate !== null && $endDate !== null) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            [$totalSoldQuantity, $totalRevenue] = $this->getTotalSoldAndRevenue($startDate, $endDate);
        } else {
            $startDate = null;
            $endDate = null;
            [$totalSoldQuantity, $totalRevenue] = $this->getTotalSoldAndRevenue();
        }

        $query = DB::table('sales_invoice_products as sip')
            ->join('sales_invoices as si', 'si.id', '=', 'sip.sales_invoice_id')
            ->join('products as p', 'p.id', '=', 'sip.product_id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->join('departments as d', 'c.department_id', '=', 'd.id')
            ->join('outlets as o', 'si.outlet_id', '=', 'o.id');

        if ($startDate !== null && $endDate !== null) {
            $query->whereBetween('si.created_at', [$startDate, $endDate]);
        }

        if ($outletId !== null) {
            $query->where('si.outlet_id', $outletId);
        }

        return $query
            ->select([
                'd.name as department_name',
                DB::raw('SUM(sip.quantity) as sold_quantity'),
                DB::raw('SUM(sip.unit_price * sip.quantity) as total_sold'),
                DB::raw('SUM(sip.quantity) / ' . ($totalSoldQuantity ?: 1) . ' * 100 as percentage_qty'),
                DB::raw('SUM(sip.quantity * sip.unit_price) / ' . ($totalRevenue ?: 1) . ' * 100 as percentage_revenue'),
            ])
            ->groupBy(
                'd.name',
            );
    }
}
