<?php
namespace App\Repositories\Room;
use App\Models\Room;
use App\Repositories\BaseEloquentRepository;

    class EloquentRoomRepository extends BaseEloquentRepository implements RoomRepositoryInterface{
        public function __construct()
        {
            $this->model = new Room();
        }

        public function search(?string $roomTypeId = null, ?string $branchId = null)
        {
            return Room::query()
                ->when($branchId, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->when($roomTypeId, function ($query) use ($roomTypeId) {
                    $query->where('room_type_id', $roomTypeId);
                })
                ->get();
        }

    }