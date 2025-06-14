<?php
namespace App\Repositories\Booking;

interface BookingRepositoryInterface{
    public function create(array $data);
    public function findById($id);
    public function getAll();
    public function update($id, array $data);
    public function delete($id);
}