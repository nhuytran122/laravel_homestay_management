<?php

namespace App\Services;

use App\Repositories\RoomType\RoomTypeRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoomTypeService
{
    private RoomTypeRepositoryInterface $repo;
    private RoomPricingService $roomPricingService;

    public function __construct(RoomTypeRepositoryInterface $repo, RoomPricingService $roomPricingService)
    {
        $this->repo = $repo;
        $this->roomPricingService = $roomPricingService;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function search(string $keyword)
    {
        return $this->repo->search($keyword);
    }


    public function create(array $data)
    {
        return DB::transaction(function () use ($data){
            $room_type = $this->repo->create($data);
            $room_pricing = $this->copyDataRoomPricing($data);
            $room_pricing['room_type_id'] = $room_type->id;
            $this->roomPricingService->create($room_pricing);   
            return $room_type->refresh();     
        });
    }

    public function update($id, array $data)
    {
        $this->getById($id);
        return DB::transaction(function () use ($data, $id){
            $room_type = $this->repo->update($id, $data);

            $current_room_pricing = $this->roomPricingService->getDefaultPricingByRoomTypeId($id);
            $room_pricing = $this->copyDataRoomPricing($data);
            $room_pricing['room_type_id'] = $id;
            $this->roomPricingService->update($current_room_pricing->id, $room_pricing);   

            return $room_type->refresh();     
        });
    }

    public function delete($id)
    {
        $room_type = $this->getById($id);

        if (!$this->canDelete($room_type)) {
            throw ValidationException::withMessages([
                'room_type_id' => "Không thể xóa vì loại phòng này có data liên quan."
            ]);
        }
        return $this->repo->delete($id);
    }

    public function getById($id)
    {
        $room_type = $this->repo->findById($id);

        if (!$room_type) {
            throw new ModelNotFoundException('Không tìm thấy loại phòng với ID: ' . $id);
        }
        return $room_type;
    }

    private function canDelete($room_type){
        return !($room_type->rooms()->exists());
    }

    private function copyDataRoomPricing($data){
        $room_pricing['base_duration'] = $data['base_duration'];
        $room_pricing['base_price'] = $data['base_price'];
        $room_pricing['extra_hour_price'] = $data['extra_hour_price'];
        $room_pricing['overnight_price'] = $data['overnight_price'];
        $room_pricing['daily_price'] = $data['daily_price'];
        $room_pricing['start_date'] = $data['start_date'] ?? null;
        $room_pricing['end_date'] = $data['end_date'] ?? null;
        $room_pricing['policy'] = $data['policy'] ?? null;
        $room_pricing['is_default'] = true;
        return $room_pricing;
    }

}