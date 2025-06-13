<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'unit', 'price', 'is_prepaid', 'description'];

    public function booking_services(){
        return $this->hasMany(BookingService::class);
    }
}