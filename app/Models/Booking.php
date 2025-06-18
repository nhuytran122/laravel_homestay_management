<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['customer_id', 'room_id', 'check_in', 'check_out',
        'guest_count', 'status', 'total_amount', 'paid_amount', 'has_send_reminder'];

    protected $casts = [
        'status' => BookingStatus::class,
        'check_in' => 'datetime',
        'check_out' => 'datetime'
    ];
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function booking_services(){
        return $this->hasMany(BookingService::class);
    }
    public function booking_extensions(){
        return $this->hasMany(BookingExtension::class);
    }
    public function booking_pricing_snapshot(){
        return $this->hasOne(BookingPricingSnapshot::class);
    }
    public function review(){
        return $this->hasOne(Review::class);
    }
    public function payments(){
        return $this->hasMany(Payment::class);
    }
    public function room(){
        return $this->belongsTo(Room::class);
    }

}