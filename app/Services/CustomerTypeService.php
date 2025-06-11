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
        $customerType = $this->repo->update($id, $data);

        if (!$customerType) {
            throw new ModelNotFoundException("Không thể cập nhật: không tìm thấy phân loại khách hàng với ID $id.");
        }

        return $customerType;
    }

    public function delete($id)
    {
        $customerType = $this->repo->findById($id);

        if (!$customerType) {
            throw new ModelNotFoundException("Không thể xóa: không tìm thấy phân loại khách hàng với ID $id.");
        }

        $has_customer = $customerType->customer()->exists();
        if ($has_customer) {
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
        $customerType = $this->repo->findById($id);

        if (!$customerType) {
            throw new ModelNotFoundException('Không tìm thấy phân loại khách hàng với ID: ' . $id);
        }
        return $customerType;
    }
}