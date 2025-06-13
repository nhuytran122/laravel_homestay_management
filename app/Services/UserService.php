<?php
namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\RoleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

    class UserService{
        private $repo;
        private $roleService;

        public function __construct(UserRepositoryInterface $repo, RoleService $roleService){
            $this->repo = $repo;
            $this->roleService = $roleService;
        }
        
        public function create($data){
            $defaultPassword = Config::get('custom.default_password');

            $data['password'] = Hash::make($data['password'] ?? $defaultPassword);
            $data['role_id'] = $data['role_id'] ?? $this->roleService->getCustomerRoleId();
            return $this->repo->create($data);
        }

        public function update($id, $data){
            return $this->repo->update($id, $data);
        }

        public function delete($id){
            $this->getById($id);
            return $this->repo->delete($id);
        }

        public function getById($id)
        {
            $customer_type = $this->repo->findById($id);

            if (!$customer_type) {
                throw new ModelNotFoundException('Không tìm thấy người dùng với ID: ' . $id);
            }
            return $customer_type;
        }
    }