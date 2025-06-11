<?php
namespace App\Repositories\CustomerType;

interface CustomerTypeRepositoryInterface{
    public function create(array $customer_type);
    public function findById($id);
    public function searchByName(string $keyword);
    public function findFirstByOrderByMinPointAsc();
    public function getAll();
    public function update($id, array $data);
    public function delete($id);
}