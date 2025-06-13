<?php

namespace App\Services;

use App\Repositories\Customer\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerService
{
    private CustomerRepositoryInterface $repo;
    private CustomerTypeService $customerTypeService;
    private UserService $userService;

    public function __construct(CustomerRepositoryInterface $repo, CustomerTypeService $customerTypeService,
        UserService $userService)
    {
        $this->repo = $repo;
        $this->customerTypeService = $customerTypeService;
        $this->userService = $userService;
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
        return DB::transaction(function () use ($data){
            $user = $this->userService->create($data);
            $customer_data = [
                'customer_type_id'  => $this->customerTypeService->getMinPointCustomerType()->id,
                'user_id' => $user->id,
            ];
            return $this->repo->create($customer_data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($data, $id){
            $customer = $this->getById($id);
            $this->userService->update($customer->user->id, $data);
            return $customer->refresh();
        });
    }

    public function delete($id)
    {
        $customer = $this->getById($id);

        if ($customer->bookings()->exists()) {
            throw ValidationException::withMessages([
                'customer_id' => "Không thể xóa vì hiện khách hàng có liên quan đến dữ liệu đơn đặt phòng."
            ]);
        }
        return DB::transaction(function () use ($customer, $id){
            $this->userService->delete($customer->user->id);
        });
    }

    public function getById($id)
    {
        $customer = $this->repo->findById($id);
        if (!$customer) {
            throw new ModelNotFoundException('Không tìm thấy khách hàng với ID: ' . $id);
        }
        return $customer;
    }
}