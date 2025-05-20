<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $table = 'employees';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nip',
        'name',
        'phone',
        'email',
        'position_id',
        'user_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'employee_outlet', 'employee_id', 'outlets_id');
    }

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class, 'employee_id');
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'employee_id');
    }
}
