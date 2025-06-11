<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    protected $fillable = [
        'item_id', 'branch_id', 'quantity'
    ];

    public function item(){
        return $this->belongsTo(InventoryItem::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }
}