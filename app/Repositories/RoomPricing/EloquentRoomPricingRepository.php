<?php
namespace App\Repositories\RoomPricing;
use App\Models\RoomPricing;
use App\Repositories\RoomPricing\RoomPricingRepositoryInterface;

    class EloquentRoomPricingRepository implements RoomPricingRepositoryInterface{
        public function findById($id)
        {
            return RoomPricing::find($id); 
        }

        public function getAll()
        {
            return RoomPricing::all();
        }
        
        public function create($data)
        {
            return RoomPricing::create($data);
        }

        public function update($id, $data)
        {
            $room_pricing = $this->findById($id);
            $room_pricing->update($data);
            return $room_pricing;
        }

        public function delete($id)
        {
            $room_pricing = $this->findById($id);
            return $room_pricing->delete();
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

    }