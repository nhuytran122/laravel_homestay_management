<?php
namespace App\Repositories\BookingExtension;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\BookingExtension\BookingExtensionRepositoryInterface;

    class EloquentBookingExtensionRepository extends BaseEloquentRepository implements BookingExtensionRepositoryInterface{
        public function __construct()
        {
            $this->model = new Booking();
        }
        
        public function findByBookingId($bookingId)
        {
            return Booking::where('parent_id', $bookingId)->get();
        }

        public function getLatestByBookingId(int $bookingId): Booking
        {
            return Booking::where('parent_id', $bookingId)
                ->orderByDesc('created_at')
                ->firstOrFail();
        }

        public function deleteLatestExtensionByBookingId(int $bookingId) {
            $extension_id = $this->getLatestByBookingId($bookingId);
            $this->delete($extension_id);
        }

        public function getUnpaidBookingExtensions()
        {
            return $this->baseUnpaidExtensionsQuery()->get();
        }

        public function expireUnpaidBookingExtensions(): int
        {
            return $this->baseUnpaidExtensionsQuery()
                ->update(['status' => BookingStatus::EXPIRED]);
        }

        public function hasUnpaidExtensionByBookingId(int $bookingId): bool
        {
            return $this->baseUnpaidExtensionsQuery()
                ->where('parent_id', $bookingId)
                ->exists();
        }

        private function baseUnpaidExtensionsQuery()
        {
            return Booking::whereNotNull('parent_id')
                ->where('status', BookingStatus::PENDING_PAYMENT)
                ->orWhere('status', BookingStatus::PENDING_CONFIRMATION);
            // return  Booking::whereNotNull('parent_id')
            //     ->where('status', BookingStatus::PENDING_PAYMENT)
            //     ->doesntHave('payments');
        }

    }