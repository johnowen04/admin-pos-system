<?php

namespace App\Models;

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
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //Relationships
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_outlet', 'outlets_id', 'employee_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'outlet_category', 'outlets_id', 'categories_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'outlet_product', 'outlets_id', 'products_id')
            ->withPivot('quantity');
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