<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Room extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['branch_id', 'room_type_id', 'room_number', 'area', 'is_active'];

    public function room_type(){
        return $this->belongsTo(RoomType::class);
    }
    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
    public function maintenance_requests(){
        return $this->hasMany(MaintenanceRequest::class);
    }
    public function room_photos(){
        return $this->hasMany(RoomPhoto::class);
    }

    public function bookings(){
        return $this->hasMany(Booking::class);
    }
}