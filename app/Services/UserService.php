<?php
namespace App\Services;

use App\Enums\RoleSystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

    class UserService{
        private $repo;

        public function __construct(UserRepositoryInterface $repo){
            $this->repo = $repo;
        }
        
        public function create($data)
        {
            $defaultPassword = Config::get('custom.default_password');
            $data['password'] = Hash::make($data['password'] ?? $defaultPassword);
            $user = $this->repo->create($data);
            $roleName = $data['role_id'] ?? RoleSystem::CUSTOMER->value;
            $user->assignRole($roleName);
            return $user;
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