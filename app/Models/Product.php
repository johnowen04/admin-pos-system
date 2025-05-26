<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'base_price',
        'buy_price',
        'sell_price',
        'min_qty',
        'units_id',
        'categories_id',
        'is_shown',
    ];

    protected $casts = [
        'is_shown' => 'boolean',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'units_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }

    public function outlets()
    {
        return $this->hasManyThrough(
            Outlet::class,
            Inventory::class,
            'product_id',
            'id',
            'id',
            'outlet_id'
        );
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'product_id', 'id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'id');
    }

    public function initialStockPerOutlet()
    {
        return $this->hasMany(StockMovement::class, 'product_id')
            ->select('product_id', 'outlet_id', DB::raw("
            SUM(
                CASE 
                    WHEN movement_type = 'purchase' THEN quantity
                    WHEN movement_type = 'sale' THEN -quantity
                    ELSE quantity
                END
            ) AS quantity
        "))
            ->groupBy('product_id', 'outlet_id');
    }

    public function purchaseInvoices()
    {
        return $this->belongsToMany(PurchaseInvoice::class, 'purchase_invoice_product', 'product_id', 'purchase_invoice_id')
            ->withPivot('quantity', 'unit_price', 'total_price')
            ->withTimestamps();
    }

    public function salesInvoices()
    {
        return $this->belongsToMany(SalesInvoice::class, 'sales_invoice_product', 'product_id', 'sales_invoice_id')
            ->withPivot('quantity', 'unit_price', 'total_price')
            ->withTimestamps();
    }
}
