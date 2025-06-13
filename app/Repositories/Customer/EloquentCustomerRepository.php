<?php
namespace App\Repositories\Customer;
use App\Models\Customer;

    class EloquentCustomerRepository implements CustomerRepositoryInterface{
        public function findById($id)
        {
            return Customer::find($id); 
        }

        public function search(string $keyword)
        {
            return Customer::whereHas('user', function ($query) use ($keyword) {
                $query->where('full_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            })->with('user')->get();
        }

        public function getAll()
        {
            return Customer::all();
        }
        
        public function create($data)
        {
            return Customer::create($data);
        }

        public function update($id, $data)
        {
            $customer = $this->findById($id);

            $customer->update($data);
            return $customer;
        }

        public function delete($id)
        {
            $customer = $this->findById($id);
            return $customer->delete();
        }

    }