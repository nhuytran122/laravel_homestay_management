<?php

namespace App\Models;

use App\Enums\BookingServiceStatus;
use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    protected $fillable = ['booking_id', 'service_id', 'quantity', 'status', 'description'];

    protected $casts = [
        'status' => BookingServiceStatus::class,
    ];

    public function booking(){
        return $this->belongsTo(Booking::class);
    }
    public function payment_detail(){
        return $this->hasOne(PaymentDetail::class);
    }

    public function service(){
        return $this->belongsTo(Service::class);
    }

}