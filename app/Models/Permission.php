<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $table = 'permissions';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'feature_id',
        'operation_id',
        'slug',
        'is_super_user_only',
    ];

    protected $casts = [
        'is_super_user_only' => 'boolean',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    public function operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id');
    }

    public function position()
    {
        return $this->belongsToMany(Position::class, 'permission_position')
            ->withTimestamps();
    }
}
