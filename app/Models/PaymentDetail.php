<?php

namespace App\Models;

use App\Enums\PaymentPurpose;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    protected $fillable = ['payment_id', 'booking_service_id', 'extension_id', 'payment_purpose', 'base_amount', 'final_amount'];

    protected $casts = [
        'payment_purpose' => PaymentPurpose::class,
    ];

    public function payment(){
        return $this->belongsTo(Payment::class);
    }

    public function booking_service(){
        return $this->belongsTo(BookingService::class);
    }

    public function booking_extension(){
        return $this->belongsTo(BookingExtension::class);
    }
}