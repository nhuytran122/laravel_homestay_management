<?php

namespace App\Services;

use App\Models\Booking;
use App\Repositories\BookingExtension\BookingExtensionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookingExtensionService
{
    private BookingExtensionRepositoryInterface $repo;
    private BookingService $bookingService;

    public function __construct(BookingExtensionRepositoryInterface $repo, BookingService $bookingService)
    {
        $this->repo = $repo;
        $this->bookingService = $bookingService;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function create(array $data)
    {
        $booking_extension = $this->bookingService->handleCreateBookingExtend($data);
        return $booking_extension;
    }

    public function delete($id)
    {
        $this->getById($id); 
        return $this->repo->delete($id);
    }

    public function getById($id): Booking
    {
        $booking_extension = $this->repo->findById($id);

        if (!$booking_extension) {
            throw new ModelNotFoundException("Không tìm thấy đơn gia hạn với ID: $id");
        }
        return $booking_extension;
    }

    public function findByBookingId(int $bookingId)
    {
        return $this->repo->findByBookingId($bookingId);
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

    private function canUpdateAndDelete(Booking $bExtension): bool
    {
        $hasPaid = $bExtension->payment_detail()->exists();
        return !$hasPaid;
    }

    public function getUnpaidBookingExtensions(){
        return $this->repo->getUnpaidBookingExtensions();
    }

    public function getLatestByBookingId(int $bookingId): Booking{
        return $this->repo->getLatestByBookingId($bookingId);
    }

    public function hasUnpaidExtensionByBookingId(int $bookingId){
        return $this->repo->hasUnpaidExtensionByBookingId($bookingId);
    }
}