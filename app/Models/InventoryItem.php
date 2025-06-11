<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['category_id', 'name', 'unit', 'price'];

    public function inventory_category(){
        return $this->belongsTo(InventoryCategory::class);
    }
    public function inventory_stocks(){
        return $this->hasMany(InventoryStock::class);
    }
    public function inventory_transactions(){
        return $this->hasMany(InventoryTransaction::class);
    }
}