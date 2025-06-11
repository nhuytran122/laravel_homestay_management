<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomPricing extends Model
{
    protected $fillable = ['base_duration', 'base_price', 'extra_hour_price', 'overnight_price', 'daily_price', 
    'start_date', 'end_date', 'policy', 'is_default', 'room_type_id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_default' => 'boolean'
        ];
    }

    public function room_type(){
        return $this->belongsTo(RoomType::class);
    }
}