<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPricingSnapshot extends Model
{
    protected $fillable = ['booking_id', 'base_duration', 'base_price', 'extra_hour_price', 'overnight_price', 'daily_price'];

    public function booking(){
        return $this->belongsTo(Booking::class);
    }
}