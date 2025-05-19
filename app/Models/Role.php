<?php

namespace App\Models;

use App\Enums\RoleLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $table = 'roles';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'level',
    ];

    protected $casts = [
        'level' => RoleLevel::class,
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function levelEnum(): RoleLevel
    {
        return RoleLevel::from($this->level);
    }
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->withTimestamps();
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'roles_id');
    }
}
