<?php

namespace App\Models;

use App\Enums\BookingExtensionStatus;
use Illuminate\Database\Eloquent\Model;

class BookingExtension extends Model
{
    protected $fillable = ['booking_id', 'extended_hours', 'status'];

    protected $casts = [
        'status' => BookingExtensionStatus::class
    ];
    public function booking(){
        return $this->belongsTo(Booking::class);
    }
    public function payment_detail(){
        return $this->hasOne(PaymentDetail::class);
    }
}