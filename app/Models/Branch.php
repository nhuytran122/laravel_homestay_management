<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Branch extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['name', 'address', 'phone', 'gate_password'];

    public function rooms(){
        return $this->hasMany(Room::class);
    }
    public function inventory_stocks(){
        return $this->hasMany(InventoryStock::class);
    }
    public function inventory_transactions(){
        return $this->hasMany(InventoryTransaction::class);
    }
    public function maintenance_requests(){
        return $this->hasMany(MaintenanceRequest::class);
    }
}