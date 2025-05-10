<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoices extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_invoices';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'invoice_number',
        'grand_total',
        'description',
        'outlets_id',
        'nip',
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
        return $this->belongsTo(Employee::class, 'nip', 'nip');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_invoice_product', 'purchase_invoice_id', 'sku')
            ->withPivot('quantity', 'unit_price', 'total_price')
            ->withTimestamps();
    }
}
