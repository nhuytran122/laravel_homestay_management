<?php
namespace App\Repositories\RoomType;

use App\Repositories\BaseRepositoryInterface;

interface RoomTypeRepositoryInterface extends BaseRepositoryInterface{
    public function search(string $keyword);
}