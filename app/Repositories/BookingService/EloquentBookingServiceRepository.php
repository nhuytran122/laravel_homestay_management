<?php
namespace App\Repositories\BookingService;

use App\Enums\BookingServiceStatus;
use App\Models\BookingService;
use App\Repositories\BookingService\BookingServiceRepositoryInterface;

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

        public function getByBookingAndPrepaidType($bookingId, bool $isPrepaid)
        {
            return BookingService::where('booking_id', $bookingId)
                ->with('service') 
                ->whereHas('service', function ($query) use ($isPrepaid) {
                    $query->where('is_prepaid', $isPrepaid);
                })
                ->get();
        }


        public function hasPostpaidService(int $bookingId): bool
        {
            return BookingService::where('booking_id', $bookingId)
                ->whereHas('service', fn($q) => $q->where('is_prepaid', false))
                ->exists();
        }

        public function existsPostpaidServiceWithoutQuantity(int $bookingId){
            return BookingService::where('booking_id', $bookingId)
                ->whereHas('service', function ($query) {
                    $query->where('is_prepaid', false);
                })
                ->where(function ($query) {
                    $query->whereNull('quantity')
                        ->orWhere('quantity', '<=', 0);
                })
                ->exists();
        }

        public function bulkUpdateServiceStatusByBookingId(int $bookingId, BookingServiceStatus $status): int
        {
            return BookingService::where('booking_id', $bookingId)
                ->update(['status' => $status->value]);
        }
    }