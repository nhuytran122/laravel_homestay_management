<?php
namespace App\Repositories\RoomPricing;

interface RoomPricingRepositoryInterface{
    public function create(array $data);
    public function findById($id);
    public function getAll();
    public function update($id, array $data);
    public function delete($id);
    public function getDefaultPricingByRoomTypeId(int $roomTypeId);
    public function isOverlapping($roomTypeId, $checkIn, $checkOut, $currentId = null);
    public function clearDefaultPricingForRoomType($roomTypeId, $excludeId = null);
    public function findApplicablePricingForRange($roomTypeId, $checkIn, $checkOut);
}