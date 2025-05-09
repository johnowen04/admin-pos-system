<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function pages()
    {
        return $this->belongsToMany(Page::class, 'role_page');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'roles_id');
    }
}
