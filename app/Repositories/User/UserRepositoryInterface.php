<?php
namespace App\Repositories\User;
use App\Models\User;

interface UserRepositoryInterface{
    public function findByEmail($email);
    public function create(array $customer_type);
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);
}