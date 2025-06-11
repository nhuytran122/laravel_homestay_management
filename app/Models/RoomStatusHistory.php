<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Model;

class RoomStatusHistory extends Model
{
    protected $fillable = ['room_id', 'status', 'started_at', 'ended_at', 'booking_id'];

    protected $casts = [
        'status' => RoomStatus::class,
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
}