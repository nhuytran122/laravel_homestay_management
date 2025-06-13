<?php

namespace App\Services;

use App\Repositories\Room\RoomRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class RoomService
{
    private RoomRepositoryInterface $repo;

    public function __construct(RoomRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function search(?string $room_type_id = null, ?string $branch_id = null)
    {
        return $this->repo->search($room_type_id, $branch_id);
    }


    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        $this->getById($id);
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        $room = $this->getById($id);

        if (!$this->canDelete($room)) {
            throw ValidationException::withMessages([
                'room_id' => "Không thể xóa vì phòng này có data liên quan."
            ]);
        }
        return $this->repo->delete($id);
    }

    public function getById($id)
    {
        $room = $this->repo->findById($id);

        if (!$room) {
            throw new ModelNotFoundException('Không tìm thấy phòng với ID: ' . $id);
        }
        return $room;
    }

    private function canDelete($room){
        return !($room->bookings()->exists() );
    }
}