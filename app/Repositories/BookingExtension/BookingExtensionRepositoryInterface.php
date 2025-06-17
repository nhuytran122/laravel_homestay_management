<?php
namespace App\Repositories\BookingExtension;

interface BookingExtensionRepositoryInterface{
    public function findById($id);
    public function getAll();
    public function create(array $data);
    public function delete($id);
    public function findByBookingId($bookingId);
    public function getLatestByBookingId(int $bookingId);
    public function getUnpaidBookingExtensions();
    public function deleteUnpaidBookingExtensions();
    public function deleteLatestExtensionByBookingId(int $bookingId);
}