<?php
namespace App\Repositories\BookingService;

use App\Enums\BookingServiceStatus;
use App\Repositories\BaseRepositoryInterface;

interface BookingServiceRepositoryInterface extends BaseRepositoryInterface{
    public function findByBookingId($bookingId);
    public function bulkUpdateServiceStatusByBookingId(int $bookingId, BookingServiceStatus $status);
    public function findBookingServicesWithoutPaymentDetailByBookingId($bookingId);
    public function existsByBookingId(int $bookingId);
    public function getByBookingAndPrepaidType($bookingId, bool $isPrepaid);
    public function hasPostpaidService(int $bookingId);
    public function existsPostpaidServiceWithoutQuantity(int $bookingId);
}