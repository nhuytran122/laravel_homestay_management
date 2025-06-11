<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\RefundType;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = ['payment_id', 'refund_type', 'refund_amount', 'status', 'vnp_transaction_no'];

    protected $casts = [
        'refund_type' => RefundType::class,
        'status' => PaymentStatus::class,
    ];

    public function payment(){
        return $this->belongsTo(Payment::class);
    }
}