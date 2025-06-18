<?php
namespace App\Repositories\RoomStatusHistory;

use App\Enums\RoomStatus;
use App\Models\RoomStatusHistory;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\RoomStatusHistory\RoomStatusHistoryRepositoryInterface;

    class EloquentRoomStatusHistoryRepository extends BaseEloquentRepository implements RoomStatusHistoryRepositoryInterface{
        public function __construct()
        {
            $this->model = new RoomStatusHistory();
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