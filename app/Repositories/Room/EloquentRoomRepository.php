<?php
namespace App\Repositories\Room;
use App\Models\Room;

    class EloquentRoomRepository implements RoomRepositoryInterface{
        public function findById($id)
        {
            return Room::with('room_type')->find($id);
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

        public function getAll()
        {
            return Room::all();
        }
        
        public function create($data)
        {
            return Room::create($data);
        }

        public function update($id, $data)
        {
            $room = $this->findById($id);
            $room->update($data);
            return $room;
        }

        public function delete($id)
        {
            $room = $this->findById($id);
            return $room->delete();
        }
    }