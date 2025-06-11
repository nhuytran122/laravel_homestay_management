<?php
namespace App\Services;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\RoleService;

    class UserService{
        private $userRepository;
        private $roleService;

        public function __construct(UserRepositoryInterface $userRepository, RoleService $roleService){
            $this->userRepository = $userRepository;
            $this->roleService = $roleService;
        }
        
        public function create($data){
            $defaultPassword = Config::get('custom.default_password');

            $data['password'] = Hash::make($data['password'] ?? $defaultPassword);
            $data['role_id'] = $data['role_id'] ?? $this->roleService->getCustomerRoleId();
            $data['is_enabled'] = $data['is_enabled'] ?? false;
            return $this->userRepository->create($data);
        }
        
    }