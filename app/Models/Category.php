<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'department_id',
        'is_shown',
    ];

    protected $casts = [
        'is_shown'=> 'boolean',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_category', 'category_id', 'outlet_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}