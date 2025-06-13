<?php

namespace App\Services;

use App\Repositories\Employee\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmployeeService
{
    private EmployeeRepositoryInterface $repo;
    private UserService $userService;

    public function __construct(EmployeeRepositoryInterface $repo, UserService $userService)
    {
        $this->repo = $repo;
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
            $employeeData = [
                'salary'  => $data['salary'],
                'user_id' => $user->id,
            ];

            return $this->repo->create($employeeData);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($data, $id){
            $employee = $this->getById($id);
            $user = $this->userService->update($employee->user->id, $data);
            $employeeData = [
                'salary'  => $data['salary'],
            ];

            return $this->repo->update($id, $employeeData);
        });
    }

    public function delete($id)
    {
        $employee = $this->getById($id);

        if (!$this->canDelete($employee)) {
            throw ValidationException::withMessages([
                'employee_id' => "Không thể xóa vì hiện nhân viên có liên quan đến dữ liệu khác."
            ]);
        }

        return DB::transaction(function () use ($employee, $id){
            $this->userService->delete($employee->user->id);
        });
    }

    public function getById($id)
    {
        $employee = $this->repo->findById($id);
        if (!$employee) {
            throw new ModelNotFoundException('Không tìm thấy nhân viên với ID: ' . $id);
        }
        return $employee;
    }

    private function canDelete($employee){
        return !($employee->inventory_transactions->exists() || $employee->maintenance_requests->exists());
    }
}