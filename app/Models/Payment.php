<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['booking_id', 'payment_type', 'payment_date', 'status', 'vnp_transaction_no', 'vnp_txt_ref', 'total_amount'];
    
    protected $casts = [
        'payment_type' => PaymentType::class,
        'status' => PaymentStatus::class,
        'payment_date' => 'datetime'
    ];

    public function booking(){
        return $this->belongsTo(Booking::class);
    }
    public function payment_details(){
        return $this->hasMany(PaymentDetail::class);
    }
    public function refund(){
        return $this->hasOne(Refund::class);
    }
}