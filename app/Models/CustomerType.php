<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    protected $fillable = ['name', 'description', 'discount_rate', 'min_point'];

    public function customers(){
        return $this->hasMany(Customer::class);
    }
}