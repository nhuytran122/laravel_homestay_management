<?php
namespace App\Repositories\Role;

use App\Enums\RoleSystem;
use Spatie\Permission\Models\Role;

    class EloquentRoleRepository implements RoleRepositoryInterface{
        public function getCustomerRoleId() {
            return Role::where('name', RoleSystem::CUSTOMER->value)->value('id');
        }
    }