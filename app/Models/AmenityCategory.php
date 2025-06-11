<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmenityCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function amentities(){
        return $this->hasMany(Amenity::class);
    }
}