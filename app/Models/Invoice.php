<?php

namespace App\Models;

use App\Contracts\ReversibleInvoice;
use App\Traits\Voidable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class Invoice extends Model implements ReversibleInvoice
{
    use SoftDeletes, Voidable;

    public $incrementing = true;
    public $timestamps = true;

    protected $commonFillable = [
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

    abstract public function products();
}
