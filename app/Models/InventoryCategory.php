<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function inventory_items(){
        return $this->hasMany(InventoryItem::class);
    }
}