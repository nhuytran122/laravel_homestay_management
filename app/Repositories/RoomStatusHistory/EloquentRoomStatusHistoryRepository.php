<?php
namespace App\Repositories\RoomStatusHistory;

use App\Enums\RoomStatus;
use App\Models\RoomStatusHistory;
use App\Repositories\RoomStatusHistory\RoomStatusHistoryRepositoryInterface;

    class EloquentRoomStatusHistoryRepository implements RoomStatusHistoryRepositoryInterface{
        public function findById($id)
        {
            return RoomStatusHistory::find($id); 
        }

        public function getAll()
        {
            return RoomStatusHistory::all();
        }
        
        public function create($data)
        {
            return RoomStatusHistory::create($data);
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

        public function existsOverlappingStatuses($roomId, $checkIn, $checkOut)
        {
            return RoomStatusHistory::where('room_id', $roomId)
                ->where('started_at', '<', $checkOut)
                ->where('ended_at', '>', $checkIn)
                ->exists();
        }

        public function getScheduleByBookingIdAndStatus(int $bookingId, RoomStatus $status)
        {
            return RoomStatusHistory::where('booking_id', $bookingId)
                ->where('status', $status) 
                ->first();
        }
        
        public function deleteByBookingId($bookingId)
        {
            return RoomStatusHistory::where('booking_id', $bookingId)->delete();
        }

    }