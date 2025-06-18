<?php
namespace App\Repositories\Employee;
use App\Models\Employee;
use App\Repositories\BaseEloquentRepository;

    class EloquentEmployeeRepository extends BaseEloquentRepository implements EmployeeRepositoryInterface{
        public function __construct()
        {
            $this->model = new Employee();
        }

        public function search(string $keyword)
        {
            return Employee::whereHas('user', function ($query) use ($keyword) {
                $query->where('full_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            })->with('user')->get();
        }
    }