<?php

namespace App\Services;

use App\Repositories\Branch\BranchRepositoryInterface;
use App\Traits\HasFileUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class BranchService
{
    use HasFileUpload;
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

    public function create(array $data, ?UploadedFile $image = null)
    {
        $branch = $this->repo->create($data);
        $this->uploadFileToCollection($branch, $image, 'images');
        return $branch;
    }

    public function update($id, array $data, ?UploadedFile $image = null)
    {
        $this->getById($id);
        $branch = $this->repo->update($id, $data);
        $this->replaceMedia($branch, $image, 'images');
        return $branch;
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