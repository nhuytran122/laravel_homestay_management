<?php
namespace App\Repositories\RoomType;

use App\Models\RoomPricing;
use App\Models\RoomType;

    class EloquentRoomTypeRepository implements RoomTypeRepositoryInterface{
        public function findById($id)
        {
            return RoomType::find($id); 
        }

        public function search(string $keyword)
        {
            return RoomType::where('name', 'like', '%' . $keyword . '%')
                ->get();
        }

        public function getAll()
        {
            return RoomType::all();
        }
        
        public function create($data)
        {
            return RoomType::create($data);
        }

        public function update($id, $data)
        {
            $room_type = $this->findById($id);
            $room_type->update($data);
            return $room_type;
        }

        public function delete($id)
        {
            $room_type = $this->findById($id);
            return $room_type->delete();
        }
    }