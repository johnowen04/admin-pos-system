<?php

namespace App\Models;

use App\Enums\PositionLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;

    protected $table = 'positions';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'level',
    ];

    protected $casts = [
        'level' => PositionLevel::class,
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function levelEnum(): PositionLevel
    {
        return PositionLevel::from($this->level);
    }
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_position')
            ->withTimestamps();
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id');
    }
}
