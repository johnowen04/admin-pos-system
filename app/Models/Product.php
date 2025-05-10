<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'sku';
    public $incrementing = false;
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
        return $this->belongsToMany(Outlet::class, 'outlet_product', 'sku', 'outlets_id')
            ->withPivot('quantity');
    }
}