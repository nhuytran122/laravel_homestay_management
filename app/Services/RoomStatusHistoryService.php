<?php

namespace App\Services;

use App\Enums\RoomStatus;
use App\Models\Booking;
use App\Repositories\RoomStatusHistory\RoomStatusHistoryRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class RoomStatusHistoryService
{
    private RoomStatusHistoryRepositoryInterface $repo;

    public function __construct(RoomStatusHistoryRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function delete($id)
    {
        $room_status_history = $this->getById($id);
        return $this->repo->delete($id);
    }

    public function getById($id)
    {
        $room_status_history = $this->repo->findById($id);

        if (!$room_status_history) {
            throw new ModelNotFoundException('Không tìm thấy lịch sử phòng với ID: ' . $id);
        }
        return $room_status_history;
    }

    public function existsOverlappingStatuses($roomId, $checkIn, $checkOut){
        return $this->repo->existsOverlappingStatuses($roomId, $checkIn, $checkOut);
    }

    public function handleStatusWhenBooking(Booking $booking): void
    {
        DB::transaction(function () use ($booking){
            // 1. Trạng thái PENDING_BOOKING trong thời gian ở
            $this->create([
                'room_id' => $booking->room_id,
                'status' => RoomStatus::BUSY,
                'started_at' => $booking->check_in,
                'ended_at' => $booking->check_out,
                'booking_id' => $booking->id,
            ]);

            // 2. Trạng thái CLEANING sau khi checkout
            $cleaningHours = Config::get('custom.cleaning_hours');
            $cleaningStart = Carbon::parse($booking->check_out);
            $cleaningEnd = $cleaningStart->copy()->addHours($cleaningHours); 
            $this->create([
                'room_id' => $booking->room_id,
                'status' => RoomStatus::CLEANING,
                'started_at' => $cleaningStart,
                'ended_at' => $cleaningEnd,
                'booking_id' => $booking->id,
            ]);
        });
    }
}