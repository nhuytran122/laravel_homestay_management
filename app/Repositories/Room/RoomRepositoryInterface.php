<?php
namespace App\Repositories\Room;

use App\Repositories\BaseRepositoryInterface;

interface RoomRepositoryInterface extends BaseRepositoryInterface{
    public function search(?string $roomTypeId = null, ?string $branchId = null);
}