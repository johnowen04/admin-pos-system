<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use SoftDeletes;

    protected $table = 'sales_invoices';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'invoice_number',
        'grand_total',
        'description',
        'outlets_id',
        'employee_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlets_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sales_invoice_product', 'sales_invoice_id', 'products_id')
            ->withPivot('quantity', 'unit_price', 'total_price')
            ->withTimestamps();
    }
}
