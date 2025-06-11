<?php

namespace App\Models;

use App\Enums\MaintenanceStatus;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $fillable = ['description', 'status', 'employee_id', 'branch_id', 'room_id'];

    protected $casts = [
        'status' => MaintenanceStatus::class
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
    public function branch(){
        return $this->belongsTo(Branch::class);
    }
    public function room(){
        return $this->belongsTo(Room::class);
    }
}