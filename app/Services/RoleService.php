<?php

namespace App\Services;

use App\Repositories\Role\RoleRepositoryInterface;
class RoleService
{
    private RoleRepositoryInterface $repo;

    public function __construct(RoleRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }
}