<?php
namespace App\Repositories\Customer;
use App\Models\Customer;
use App\Repositories\BaseEloquentRepository;

    class EloquentCustomerRepository extends BaseEloquentRepository implements CustomerRepositoryInterface{

        public function __construct()
        {
            $this->model = new Customer();
        }
        
        public function search(string $keyword)
        {
            return Customer::whereHas('user', function ($query) use ($keyword) {
                $query->where('full_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            })->with('user')->get();
        }
    }