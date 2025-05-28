<?php

namespace App\Models;

class SalesInvoice extends Invoice
{
    protected $table = 'sales_invoices';
    protected $primaryKey = 'id';

    protected $fillable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = $this->commonFillable;
    }

    public function reversedQuantityFor($item): int
    {
        return +$item->quantity;
    }

    // Relationships
    public function products()
    {
        return $this->belongsToMany(Product::class, 'sales_invoice_products', 'sales_invoice_id', 'product_id')
            ->withPivot('quantity', 'base_price', 'unit_price', 'total_price')
            ->withTimestamps();
    }
}
