<?php
namespace App\Repositories\Booking;

use App\Repositories\BaseRepositoryInterface;

interface BookingRepositoryInterface extends BaseRepositoryInterface{
    public function getListBookingsByCustomerID($id);
    public function searchBookingsByCustomer(int $customerId, ?array $filters, int $perPage = 10);
}