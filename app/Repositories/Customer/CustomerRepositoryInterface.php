<?php
namespace App\Repositories\Customer;

interface CustomerRepositoryInterface{
    public function create(array $customer);
    public function findById($id);
    public function search(string $keyword);
    public function getAll();
    public function update($id, array $data);
    public function delete($id);
}