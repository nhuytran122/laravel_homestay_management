<?php

namespace App\Services;

use App\Helpers\DiscountHelper;
use App\Models\BookingExtension;
use App\Repositories\BookingExtension\BookingExtensionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class BookingExtensionService
{
    private BookingExtensionRepositoryInterface $repo;

    public function __construct(BookingExtensionRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function create(array $data)
    {
        $booking_extension = $this->repo->create($data);
        return $booking_extension;
    }

    public function delete($id)
    {
        $this->getById($id); 
        return $this->repo->delete($id);
    }

    public function getById($id): BookingExtension
    {
        $booking_extension = $this->repo->findById($id);

        if (!$booking_extension) {
            throw new ModelNotFoundException("Không tìm thấy đơn gia hạn với ID: $id");
        }
        return $booking_extension;
    }

    public function findByBookingId(int $bookingId)
    {
        return $this->findByBookingId($bookingId);
    }

    public function deleteLatestExtensionByBookingId(int $bookingId): void
    {
        $this->repo->deleteLatestExtensionByBookingId($bookingId);
    }

    public function deleteById(int $id): void
    {
        $bExtension = $this->getById($id);
        if ($this->canUpdateAndDelete($bExtension)) {
            $this->delete($id);
        }
    }

    public function calculateRawTotalAmountBookingExtension(BookingExtension $bookingExtension): float
    {
        $booking = $bookingExtension->booking;
        $roomType = $booking->room->room_type;

        $extraHourPrice = $booking->booking_pricing_snapshot->extra_hour_price;
        
        $isDorm = false;
        if (str_contains(strtoupper($roomType->name), 'DORM')) {
            $isDorm = true;
        }
        $guestCount = $booking->guest_count;

        $adjustedPrice = $isDorm ? $extraHourPrice * $guestCount : $extraHourPrice;

        return $adjustedPrice * $bookingExtension->extended_hours;
    }

    public function calculateFinalExtensionAmount(BookingExtension $bookingExtension) : float{
        $rawPrice = $this->calculateRawTotalAmountBookingExtension($bookingExtension);
        $discount = DiscountHelper::calculateDiscountAmount($rawPrice, $bookingExtension->booking->customer);
        return $rawPrice - $discount;
    }

    private function canUpdateAndDelete(BookingExtension $bExtension): bool
    {
        $hasPaid = $bExtension->payment_detail()->exists();
        return !$hasPaid;
    }

    public function getUnpaidBookingExtensions(){
        return $this->repo->getUnpaidBookingExtensions();
    }

    public function getLatestByBookingId(int $bookingId): BookingExtension{
        return $this->repo->getLatestByBookingId($bookingId);
    }

}