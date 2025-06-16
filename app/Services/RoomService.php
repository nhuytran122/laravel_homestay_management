<?php

namespace App\Services;

use App\Repositories\Room\RoomRepositoryInterface;
use App\Traits\HasFileUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class RoomService
{
    use HasFileUpload;
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


    public function create(array $data, ?UploadedFile $image = null)
    {
        $room = $this->repo->create($data);
        $this->uploadFileToCollection($room, $image, 'images');
        return $room;
    }

    public function update($id, array $data, ?UploadedFile $image = null)
    {
        $this->getById($id);
        $room = $this->repo->update($id, $data);
        $this->replaceMedia($room, $image, 'images');
        return $room;
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