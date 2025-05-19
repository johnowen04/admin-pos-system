<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feature extends Model
{
    use SoftDeletes;

    protected $table = 'features';
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
    public function operations()
    {
        return $this->hasManyThrough(
            Operation::class,
            Permission::class,
            'feature_id', // Foreign key on the feature_operation table
            'id', // Foreign key on the operations table
            'id', // Local key on the features table
            'operation_id' // Local key on the feature_operation table
        );
    }
}
