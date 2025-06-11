<?php
namespace App\Repositories\CustomerType;
use App\Models\CustomerType;

    class EloquentCustomerTypeRepository implements CustomerTypeRepositoryInterface{
        public function findById($id)
        {
            return CustomerType::find($id); 
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

        public function getAll()
        {
            return CustomerType::all();
        }
        
        public function create($data)
        {
            return CustomerType::create($data);
        }

        public function update($id, $data)
        {
            $customer_type = $this->findById($id);
            if (!$customer_type) return null;

            $customer_type->update($data);
            return $customer_type;
        }

        public function delete($id)
        {
            $customer_type = $this->findById($id);
            if (!$customer_type) return false;

            return $customer_type->delete();
        }

    }