<?php
namespace App\Repositories\RoomStatusHistory;

use App\Enums\RoomStatus;

interface RoomStatusHistoryRepositoryInterface{
    public function create(array $data);
    public function findById($id);
    public function getAll();
    public function delete($id);
    public function existsOverlappingStatuses($roomId, $checkIn, $checkOut);
    public function getScheduleByBookingIdAndStatus(int $bookingId, RoomStatus $status);
}