<?php 

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportService
{
    public function getProductSalesReportData($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        return DB::table('sales_invoice_product as sip')
            ->join('sales_invoices as si', 'si.id', '=', 'sip.sales_invoice_id')
            ->join('products as p', 'p.id', '=', 'sip.product_id')
            ->join('categories as c', 'p.categories_id', '=', 'c.id')
            ->join('outlets as o', 'si.outlet_id', '=', 'o.id')
            ->whereBetween('si.created_at', [$startDate, $endDate])
            ->select([
                DB::raw('DATE(si.created_at) as sale_date'),
                'p.name as product_name',
                'p.sku as product_sku',
                'o.name as outlet_name',
                'c.name as product_category',
                'sip.base_price',
                'sip.unit_price',
                DB::raw('SUM(sip.quantity) as total_quantity'),
                DB::raw('SUM(sip.total_price) as total_jual'),
                DB::raw('SUM(sip.quantity * sip.base_price) as total_hpp'),
                DB::raw('SUM(sip.total_price - (sip.quantity * sip.base_price)) as laba_kotor'),
            ])
            ->groupBy('sale_date', 'outlet_name', 'product_name', 'product_sku', 'product_category', 'sip.base_price', 'sip.unit_price')
            ->orderBy('sale_date')
            ->get();
    }
}
