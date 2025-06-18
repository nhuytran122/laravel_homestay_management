<?php
namespace App\Repositories\CustomerType;

interface CustomerTypeRepositoryInterface{
    public function searchByName(string $keyword);
    public function findFirstByOrderByMinPointAsc();
}