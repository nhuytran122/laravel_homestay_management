<?php
namespace App\Repositories\Branch;

use App\Repositories\BaseRepositoryInterface;

interface BranchRepositoryInterface extends BaseRepositoryInterface{
    public function search(string $keyword);
}