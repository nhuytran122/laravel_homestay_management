<?php

namespace App\Services;

use App\Enums\RoomStatus;
use App\Models\Booking;
use App\Models\BookingExtension;
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

    public function getScheduleByBookingIdAndStatus(int $bookingId, RoomStatus $status)
    {
        return $this->repo->getScheduleByBookingIdAndStatus($bookingId, $status);
    }


    public function existsOverlappingStatuses($roomId, $checkIn, $checkOut){
        return $this->repo->existsOverlappingStatuses($roomId, $checkIn, $checkOut);
    }

    public function handleStatusWhenBooking(Booking $booking): void
    {
        DB::transaction(function () use ($booking){
            // 1. Trạng thái BOOKED trong thời gian ở
            $this->create([
                'room_id' => $booking->room_id,
                'status' => RoomStatus::BOOKED,
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

    // public function handleBookingExtensions(BookingExtension $bookingExtension)
    // {
    //     $booking = $bookingExtension->booking; 
    //     $cleaningHours = Config::get('custom.cleaning_hours');
    //     $newCheckout = Carbon::parse($booking->check_out);
    //     $busyStatus = $this->getScheduleByBookingIdAndStatus($booking->id, RoomStatus::BOOKED);
        
    //     $busyStatus->ended_at = $newCheckout;
    //     $busyStatus->save();

    //     $cleanStatus = $this->getScheduleByBookingIdAndStatus($booking->id, RoomStatus::CLEANING);
    //     $cleanStatus->started_at = $newCheckout;
    //     $cleanStatus->ended_at = $newCheckout->copy()->addHours($cleaningHours);
    //     $cleanStatus->save();
    // }

    public function handleStatusWhenBookingExtend(Booking $bookingExtension)
    {
        DB::transaction(function () use ($bookingExtension){
            $parent_booking = $bookingExtension->parent; 
            $originalCheckout = $parent_booking->check_out;

            $newCheckout = $bookingExtension->check_out;
            $roomId = $bookingExtension->room_id;
            
            $cleaningHours = Config::get('custom.cleaning_hours');
            $this->create([
                    'room_id' => $roomId,
                    'booking_id' => $bookingExtension->id,
                    'status' => RoomStatus::EXTENDED,
                    'started_at' => $originalCheckout,
                    'ended_at' => $newCheckout
            ]);
            $cleanStatus = $this->getScheduleByBookingIdAndStatus($bookingExtension->parent_id, RoomStatus::CLEANING);
            $cleanStatus->started_at = $newCheckout;
            $cleanStatus->ended_at = $newCheckout->copy()->addHours($cleaningHours);
            $cleanStatus->save();
        });
    }

    public function deleteByBookingId($bookingId){
        return $this->repo->deleteByBookingId($bookingId);
    }

    public function isOverlappingRoomWithExtension(
            Booking $booking,
            Carbon $newCheckOut
    ): bool {
            $CLEANING_HOURS = Config::get('custom.cleaning_hours');
            $roomId = $booking->room_id;
            $currentCheckOut = $booking->check_out;

            $checkOutWithBuffer = $currentCheckOut->copy()->addHours($CLEANING_HOURS);
            $newCheckOutWithBuffer = $newCheckOut->copy()->addHours($CLEANING_HOURS);

            return $this->existsOverlappingStatuses($roomId, $checkOutWithBuffer, $newCheckOutWithBuffer, $booking->id);
    }

}