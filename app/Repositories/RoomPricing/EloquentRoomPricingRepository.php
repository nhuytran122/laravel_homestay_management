<?php
namespace App\Repositories\RoomPricing;
use App\Models\RoomPricing;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\RoomPricing\RoomPricingRepositoryInterface;

    class EloquentRoomPricingRepository extends BaseEloquentRepository implements RoomPricingRepositoryInterface{
        public function __construct()
        {
            $this->model = new RoomPricing();
        }

        public function getDefaultPricingByRoomTypeId(int $roomTypeId)
        {
            return RoomPricing::where('room_type_id', $roomTypeId)
                ->where('is_default', true)
                ->first();
        }

        public function isOverlapping($roomTypeId, $startDate, $endDate, $currentId = null)
        {
            return RoomPricing::where('room_type_id', $roomTypeId)
                ->where('is_default', false)
                ->when($currentId, function ($query) use ($currentId) {
                    $query->where('id', '<>', $currentId);
                })
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where(function ($q) use ($endDate) {
                        $q->whereNull('start_date')->orWhere('start_date', '<=', $endDate);
                    })->where(function ($q) use ($startDate) {
                        $q->whereNull('end_date')->orWhere('end_date', '>=', $startDate);
                    });
                })
                ->exists();
        }

        public function clearDefaultPricingForRoomType($roomTypeId, $excludeId = null)
        {
            $query = RoomPricing::where('room_type_id', $roomTypeId);
            if ($excludeId !== null) {
                $query->where('id', '!=', $excludeId);
            }
            return $query->update(['is_default' => false]);
        }

        public function findApplicablePricingForRange($roomTypeId, $checkIn, $checkOut)
        {
            return RoomPricing::where('room_type_id', $roomTypeId)
                ->where('start_date', '<=', $checkIn)
                ->where('end_date', '>=', $checkOut)
                ->first();
        }

        public function findDefaultPricingByRoomTypeId($roomTypeId)
        {
            return RoomPricing::where('room_type_id', $roomTypeId)
                ->where('is_default', true)
                ->first();
        }
    }