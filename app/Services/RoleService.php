<?php

namespace App\Services;

use App\Repositories\Role\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleService
{
    private RoleRepositoryInterface $repo;

    public function __construct(RoleRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getCustomerRoleId()
    {
        $role_customer_id = $this->repo->getCustomerRoleId();

        if (!$role_customer_id) {
            throw new ModelNotFoundException('Không tìm thấy mã vai trò khách hàng');
        }
        return $role_customer_id;
    }
}