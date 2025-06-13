<?php
namespace App\Repositories\Employee;
use App\Models\Employee;

    class EloquentEmployeeRepository implements EmployeeRepositoryInterface{
        public function findById($id)
        {
            return Employee::find($id); 
        }

        public function search(string $keyword)
        {
            return Employee::whereHas('user', function ($query) use ($keyword) {
                $query->where('full_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            })->with('user')->get();
        }

        public function getAll()
        {
            return Employee::all();
        }
        
        public function create($data)
        {
            return Employee::create($data);
        }

        public function update($id, $data)
        {
            $employee = $this->findById($id);

            $employee->update($data);
            return $employee;
        }

        public function delete($id)
        {
            $employee = $this->findById($id);
            return $employee->delete();
        }

    }