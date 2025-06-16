<?php
namespace App\Repositories\BookingExtension;
use App\Models\BookingExtension;
use App\Repositories\BookingExtension\BookingExtensionRepositoryInterface;

    class EloquentBookingExtensionRepository implements BookingExtensionRepositoryInterface{
        public function findById($id)
        {
            return BookingExtension::find($id); 
        }

        public function getAll()
        {
            return BookingExtension::all();
        }
    
        public function delete($id)
        {
            $booking = $this->findById($id);
            return $booking->delete();
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

        private function unpaidBookingExtensionsQuery()
        {
            return BookingExtension::whereNotIn('id', function ($query) {
                $query->select('extension_id')
                    ->from('payment_details')
                    ->whereNotNull('extension_id');
            });
        }

        public function getUnpaidBookingExtensions()
        {
            return $this->unpaidBookingExtensionsQuery()->get();
        }

        public function deleteUnpaidBookingExtensions(): int
        {
            return $this->unpaidBookingExtensionsQuery()->delete();
        }

    }