<?php
namespace App\Repositories\Room;

interface RoomRepositoryInterface{
    public function create(array $data);
    public function findById($id);
    public function search(?string $roomTypeId = null, ?string $branchId = null);
    public function getAll();
    public function update($id, array $data);
    public function delete($id);
}