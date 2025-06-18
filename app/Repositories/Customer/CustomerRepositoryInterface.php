<?php
namespace App\Repositories\Customer;

use App\Repositories\BaseRepositoryInterface;

interface CustomerRepositoryInterface extends BaseRepositoryInterface{
    public function search(string $keyword);
}