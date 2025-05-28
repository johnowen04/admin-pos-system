<?php

namespace App\Models;

use App\Contracts\ReversibleInvoice;
use App\Traits\Voidable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model implements ReversibleInvoice
{
    use SoftDeletes, Voidable;

    protected $table = 'sales_invoices';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'invoice_number',
        'grand_total',
        'description',
        'outlet_id',
        'employee_id',
        'created_by',
        'is_voided',
        'void_reason',
        'voided_by',
        'voided_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reversedQuantityFor($item): int
    {
        return +$item->quantity;
    }

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sales_invoice_products', 'sales_invoice_id', 'product_id')
            ->withPivot('quantity', 'base_price', 'unit_price', 'total_price')
            ->withTimestamps();
    }
}
