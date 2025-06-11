<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function customer_type(){
        return $this->belongsTo(CustomerType::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function bookings(){
        return $this->hasMany(Booking::class);
    }
}