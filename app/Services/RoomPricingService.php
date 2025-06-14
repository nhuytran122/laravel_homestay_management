<?php

namespace App\Services;

use App\Repositories\RoomPricing\RoomPricingRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoomPricingService
{
    private RoomPricingRepositoryInterface $repo;

    public function __construct(RoomPricingRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (!empty($data['is_default']) && $data['is_default']) {
                $this->repo->clearDefaultPricingForRoomType($data['room_type_id']);
            }
            return $this->repo->create($data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $roomPricing = $this->repo->findById($id);

            if (!empty($data['is_default']) && $data['is_default']) {
                $this->repo->clearDefaultPricingForRoomType($roomPricing->room_type_id, $roomPricing->id);
            }
            return $this->repo->update($id, $data);
        });
    }

    public function delete($id)
    {
        $room_pricing = $this->getById($id);

        if ($room_pricing->is_default) {
            throw ValidationException::withMessages([
                'room_pricing_id' => "Không thể xóa vì đây là giá mặc định của loại phòng."
            ]);
        }
        return $this->repo->delete($id);
    }

    public function getById($id)
    {
        $room_pricing = $this->repo->findById($id);

        if (!$room_pricing) {
            throw new ModelNotFoundException('Không tìm thấy giá phòng với ID: ' . $id);
        }
        return $room_pricing;
    }

    public function getDefaultPricingByRoomTypeId(int $roomTypeId)
    {
        $defaultPricing = $this->repo->getDefaultPricingByRoomTypeId($roomTypeId);
        if (!$defaultPricing) {
            throw new ModelNotFoundException("Không tìm thấy giá mặc định cho loại phòng ID: $roomTypeId");
        }
        return $defaultPricing;
    }

    public function getApplicablePricingForRange($roomTypeId, $checkIn, $checkOut){
        return $this->repo->findApplicablePricingForRange($roomTypeId, $checkIn, $checkOut);
    }

    public function isOverlappingTime($roomTypeId, $startDate, $endDate, $currentId = null){
        return $this->repo->isOverlapping($roomTypeId, $startDate, $endDate, $currentId);
    }
}