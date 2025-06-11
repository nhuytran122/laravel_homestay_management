<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    public function amenity_category(){
        return $this->belongsTo(AmenityCategory::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_amenities')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}