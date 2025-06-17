<?php
namespace App\Repositories\BookingService;

use App\Enums\BookingServiceStatus;

interface BookingServiceRepositoryInterface{
    public function findById($id);
    public function getAll();
    public function delete($id);
    public function findByBookingId($bookingId);
    public function bulkUpdateServiceStatusByBookingId(int $bookingId, BookingServiceStatus $status);
    public function findBookingServicesWithoutPaymentDetailByBookingId($bookingId);
    public function existsByBookingId(int $bookingId);
    public function getByBookingAndPrepaidType($bookingId, bool $isPrepaid);
    public function hasPostpaidService(int $bookingId);
    public function existsPostpaidServiceWithoutQuantity(int $bookingId);
}