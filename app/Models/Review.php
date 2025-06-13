<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Review extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['booking_id', 'rating', 'comment'];

    public function booking(){
        return $this->belongsTo(Booking::class);
    }
}