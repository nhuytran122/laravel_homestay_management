<?php
namespace App\Repositories\BookingService;

interface BookingServiceRepositoryInterface{
    public function findById($id);
    public function getAll();
    public function delete($id);
    public function deleteByBookingId($bookingId);
    public function findByBookingId($bookingId);
    public function findBookingServicesWithoutPaymentDetailByBookingId($bookingId);
    public function existsByBookingId(int $bookingId);
}