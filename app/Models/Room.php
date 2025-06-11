<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
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
}