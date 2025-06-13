<?php

namespace App\Services;

use App\Repositories\Branch\BranchRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class BranchService
{
    private BranchRepositoryInterface $repo;

    public function __construct(BranchRepositoryInterface $repo)
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
        $branch = $this->getById($id);

        if (!$this->canDelete($branch)) {
            throw ValidationException::withMessages([
                'branch_id' => "Không thể xóa vì chi nhánh này có data liên quan."
            ]);
        }
        return $this->repo->delete($id);
    }

    public function getById($id)
    {
        $branch = $this->repo->findById($id);

        if (!$branch) {
            throw new ModelNotFoundException('Không tìm thấy chi nhánh với ID: ' . $id);
        }
        return $branch;
    }

    private function canDelete($branch){
        return !($branch->rooms()->exists() || $branch->inventory_stocks()->exists() || $branch->inventory_transactions()->exists());
    }
}