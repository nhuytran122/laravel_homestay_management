<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['user_id', 'customer_type_id'];
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