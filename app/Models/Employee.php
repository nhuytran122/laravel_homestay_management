<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['salary', 'user_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function inventory_transactions(){
        return $this->hasMany(InventoryTransaction::class);
    }
    public function maintenance_requests(){
        return $this->hasMany(MaintenanceRequest::class);
    }
}