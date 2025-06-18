<?php
namespace App\Repositories\User;
use App\Models\User;
use App\Repositories\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface{
    public function findByEmail($email);
}