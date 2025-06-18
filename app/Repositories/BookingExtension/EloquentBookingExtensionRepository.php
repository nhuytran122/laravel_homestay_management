<?php
namespace App\Repositories\BookingExtension;

use App\Enums\BookingExtensionStatus;
use App\Models\BookingExtension;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\BookingExtension\BookingExtensionRepositoryInterface;

    class EloquentBookingExtensionRepository extends BaseEloquentRepository implements BookingExtensionRepositoryInterface{
        
        public function __construct()
        {
            $this->model = new BookingExtension();
        }

        public function findByBookingId($bookingId)
        {
            return BookingExtension::where('booking_id', $bookingId)->get();
        }

        public function getLatestByBookingId(int $bookingId): BookingExtension
        {
            return BookingExtension::where('booking_id', $bookingId)
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
                ->update(['status' => BookingExtensionStatus::EXPIRED]);
        }

        public function hasUnpaidExtensionByBookingId(int $bookingId): bool
        {
            return $this->baseUnpaidExtensionsQuery()
                ->where('booking_id', $bookingId)
                ->exists();
        }

        private function baseUnpaidExtensionsQuery()
        {
            return BookingExtension::where('status', BookingExtensionStatus::PENDING)
                ->whereNotIn('id', function ($query) {
                    $query->select('extension_id')
                        ->from('payment_details')
                        ->whereNotNull('extension_id');
                });
        }

    }