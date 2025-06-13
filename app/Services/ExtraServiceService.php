<?php

namespace App\Services;

use App\Repositories\Service\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ExtraServiceService
{
    private ServiceRepositoryInterface $repo;

    public function __construct(ServiceRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function search(string $keyword)
    {
        return $this->repo->search($keyword);
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
        $service = $this->getById($id);

        if (!$this->canDelete($service)) {
            throw ValidationException::withMessages([
                'service_id' => "Không thể xóa vì dịch vụ này có data liên quan."
            ]);
        }
        return $this->repo->delete($id);
    }

    public function getById($id)
    {
        $service = $this->repo->findById($id);

        if (!$service) {
            throw new ModelNotFoundException('Không tìm thấy dịch vụ với ID: ' . $id);
        }
        return $service;
    }

    private function canDelete($service){
        return !($service->booking_services()->exists());
    }
}