<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operation extends Model
{
    use SoftDeletes;

    protected $table = 'operations';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function features()
    {
        return $this->hasManyThrough(
            Feature::class,
            Permission::class,
            'operation_id', // Foreign key on the feature_operation table
            'id', // Foreign key on the features table
            'id', // Local key on the operations table
            'feature_id' // Local key on the feature_operation table
        );
    }
}
