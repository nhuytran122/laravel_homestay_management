<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'role_name',
        'description'
    ];
    public $timestamps = false;

    public function users(){
        return $this->hasMany(User::class);
    }
}