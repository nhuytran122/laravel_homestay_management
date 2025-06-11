<?php
namespace App\Repositories\Role;
use App\Models\Role;

    class EloquentRoleRepository implements RoleRepositoryInterface{
        public function getCustomerRoleId() {
            return Role::where('name', 'CUSTOMER')->value('id');
        }
    }