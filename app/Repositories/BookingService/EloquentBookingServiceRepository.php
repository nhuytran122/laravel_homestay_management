<?php
namespace App\Repositories\BookingService;
use App\Models\BookingService;
use App\Repositories\BookingService\BookingServiceRepositoryInterface;
use App\Services\BookingExtraServiceService;

    class EloquentBookingServiceRepository implements BookingServiceRepositoryInterface{
        public function findById($id)
        {
            return BookingService::find($id); 
        }

        public function getAll()
        {
            return BookingService::all();
        }
    
        public function delete($id)
        {
            $booking = $this->findById($id);
            return $booking->delete();
        }

        public function deleteByBookingId($bookingId)
        {
            return BookingService::where('booking_id', $bookingId)->delete();
        }

        public function findByBookingId($bookingId)
        {
            return BookingService::where('booking_id', $bookingId)->get();
        }

        public function findBookingServicesWithoutPaymentDetailByBookingId($bookingId)
        {
            return BookingService::where('booking_id', $bookingId)
                ->whereHas('booking', function ($query) {
                    $query->where('status', '!=', 'CANCELLED');
                })
                ->whereDoesntHave('payment_detail')
                ->get();
        }

        public function existsByBookingId(int $bookingId): bool
        {
            return BookingService::where('booking_id', $bookingId)->exists();
        }
    }