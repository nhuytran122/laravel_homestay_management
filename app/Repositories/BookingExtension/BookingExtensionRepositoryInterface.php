<?php
namespace App\Repositories\BookingExtension;

use App\Repositories\BaseRepositoryInterface;

interface BookingExtensionRepositoryInterface extends BaseRepositoryInterface{
    public function findByBookingId($bookingId);
    public function getLatestByBookingId(int $bookingId);
    public function getUnpaidBookingExtensions();
    public function expireUnpaidBookingExtensions();
    public function deleteLatestExtensionByBookingId(int $bookingId);
    public function hasUnpaidExtensionByBookingId(int $bookingId);
}