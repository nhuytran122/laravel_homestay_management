<?php
namespace App\Repositories\User;
use App\Models\User;

    class EloquentUserRepository implements UserRepositoryInterface{
        public function create($data){
            return User::create($data);
        }

        public function update($id, $data)
        {
            $user = $this->findById($id);
            $user->update($data);
            return $user;
        }

        public function delete($id)
        {
            $user = $this->findById($id);
            return $user->delete();
        }

        public function findById($id)
        {
            return User::find($id);
        }

        public function findByEmail($email)
        {
            return User::where('email', $email)->first();;
        }
    }