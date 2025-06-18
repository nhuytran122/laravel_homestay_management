<?php
namespace App\Repositories\Refund;

use App\Repositories\BaseRepositoryInterface;

interface RefundRepositoryInterface extends BaseRepositoryInterface{
    public function search(?array $filters, int $perPage = 10);
}