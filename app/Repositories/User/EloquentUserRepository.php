<?php
namespace App\Repositories\User;
use App\Models\User;
use App\Repositories\BaseEloquentRepository;

    class EloquentUserRepository extends BaseEloquentRepository implements UserRepositoryInterface{
        public function __construct()
        {
            $this->model = new User();
        }
        
        public function findByEmail($email)
        {
            return User::where('email', $email)->first();;
        }
    }