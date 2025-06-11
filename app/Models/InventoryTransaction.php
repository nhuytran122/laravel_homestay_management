<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = ['item_id', 'branch_id', 'employee_id', 'quantity', 'transaction_type'];

    protected $casts = [
        'transaction_type' => TransactionType::class
    ];

    public function item(){
        return $this->belongsTo(InventoryItem::class);
    }
    public function branch(){
        return $this->belongsTo(Branch::class);
    }
    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}