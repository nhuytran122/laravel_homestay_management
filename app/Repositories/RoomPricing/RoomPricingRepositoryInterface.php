<?php
namespace App\Repositories\RoomPricing;

use App\Repositories\BaseRepositoryInterface;

interface RoomPricingRepositoryInterface extends BaseRepositoryInterface{
    public function getDefaultPricingByRoomTypeId(int $roomTypeId);
    public function isOverlapping($roomTypeId, $checkIn, $checkOut, $currentId = null);
    public function clearDefaultPricingForRoomType($roomTypeId, $excludeId = null);
    public function findApplicablePricingForRange($roomTypeId, $checkIn, $checkOut);
}