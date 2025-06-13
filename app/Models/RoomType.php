<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RoomType extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['name', 'max_guest', 'description'];

    public function rooms(){
        return $this->hasMany(Room::class);
    }

    public function room_pricings(){
        return $this->hasMany(RoomPricing::class);
    }
}