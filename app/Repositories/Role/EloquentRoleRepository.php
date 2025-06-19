<?php
namespace App\Repositories\Role;

use App\Enums\RoleSystem;
use App\Repositories\BaseEloquentRepository;
use Spatie\Permission\Models\Role;

    class EloquentRoleRepository extends BaseEloquentRepository implements RoleRepositoryInterface{
        public function __construct(){
            $this->model = new Role();
        }
        
        public function getCustomerRoleId() {
            return Role::where('name', RoleSystem::CUSTOMER->value)->value('id');
        }
    }