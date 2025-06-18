<?php
namespace App\Repositories\Service;

use App\Repositories\BaseRepositoryInterface;

interface ServiceRepositoryInterface extends BaseRepositoryInterface{
    public function search(string $keyword);
    public function findByIsPrepaid(bool $isPrepaid);
}