<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = ['name', 'max_guest', 'description'];

    public function rooms(){
        return $this->hasMany(Room::class);
    }

    public function room_pricings(){
        return $this->hasMany(RoomPricing::class);
    }
}