<?php
namespace App\Repositories\CustomerType;
use App\Models\CustomerType;
use App\Repositories\BaseEloquentRepository;

    class EloquentCustomerTypeRepository extends BaseEloquentRepository implements CustomerTypeRepositoryInterface{
        public function __construct()
        {
            $this->model = new CustomerType();
        }
        public function searchByName(string $keyword)
        {
            return CustomerType::where('name', 'like', '%' . $keyword . '%')->get();
        }

        public function findFirstByOrderByMinPointAsc()
        {
            $customerType = CustomerType::orderBy('min_point', 'asc')->first();
            return $customerType;
        }

    }