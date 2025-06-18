<?php
namespace App\Repositories\Employee;

use App\Repositories\BaseRepositoryInterface;

interface EmployeeRepositoryInterface extends BaseRepositoryInterface{
    public function search(string $keyword);
}