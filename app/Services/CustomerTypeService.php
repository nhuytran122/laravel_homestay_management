<?php

namespace App\Services;

use App\Repositories\CustomerType\CustomerTypeRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CustomerTypeService
{
    private CustomerTypeRepositoryInterface $repo;

    public function __construct(CustomerTypeRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function searchByName(string $keyword)
    {
        return $this->repo->searchByName($keyword);
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
        $customer_type = $this->getById($id);

        if ($customer_type->customers()->exists()) {
            throw ValidationException::withMessages([
                'customer_type_id' => "Không thể xóa vì hiện có khách hàng thuộc phân loại này."
            ]);
        }
        return $this->repo->delete($id);
    }

    public function getMinPointCustomerType()
    {
        return $this->repo->findFirstByOrderByMinPointAsc();
    }

    public function getById($id)
    {
        $customer_type = $this->repo->findById($id);

        if (!$customer_type) {
            throw new ModelNotFoundException('Không tìm thấy phân loại khách hàng với ID: ' . $id);
        }
        return $customer_type;
    }
}