<?php
namespace App\Repositories\RoomStatusHistory;

interface RoomStatusHistoryRepositoryInterface{
    public function create(array $data);
    public function findById($id);
    public function getAll();
    public function delete($id);
    public function existsOverlappingStatuses($roomId, $checkIn, $checkOut);
}