<?php

namespace App\Models;

use App\Enums\OutletType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use SoftDeletes;

    protected $table = 'outlets';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'type',
        'status',
        'phone',
        'whatsapp',
        'email',
        'address',
    ];

    protected $casts = [
        'type' => OutletType::class,
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //Relationships
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_outlet', 'outlet_id', 'employee_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'outlet_category', 'outlet_id', 'categories_id');
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            Inventory::class,
            'outlet_id',
            'id',
            'id',
            'product_id'
        );
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'outlet_id', 'id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'outlet_id', 'id');
    }

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class, 'outlet_id');
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'outlet_id');
    }
}
