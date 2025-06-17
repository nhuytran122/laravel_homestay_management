<?php
namespace App\Repositories\Refund;

interface RefundRepositoryInterface{
    public function create(array $data);
    public function findById($id);
    public function search(?array $filters, int $perPage = 10);
    public function getAll();
    public function update($id, array $data);
    public function delete($id);
}