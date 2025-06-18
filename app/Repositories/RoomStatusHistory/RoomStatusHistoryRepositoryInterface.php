<?php
namespace App\Repositories\RoomStatusHistory;

use App\Enums\RoomStatus;
use App\Repositories\BaseRepositoryInterface;

interface RoomStatusHistoryRepositoryInterface extends BaseRepositoryInterface{
    public function existsOverlappingStatuses($roomId, $checkIn, $checkOut);
    public function getScheduleByBookingIdAndStatus(int $bookingId, RoomStatus $status);
    public function deleteByBookingId($bookingId);
}