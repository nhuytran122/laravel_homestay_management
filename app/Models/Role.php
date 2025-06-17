<?php

namespace App\Models;

use App\Enums\RoleSystem;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'role_name',
        'description'
    ];
    protected $casts = [
        'role_name' => RoleSystem::class,
    ];
    public $timestamps = false;

    public function users(){
        return $this->hasMany(User::class);
    }
}