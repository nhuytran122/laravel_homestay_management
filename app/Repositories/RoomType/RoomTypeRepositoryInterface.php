<?php
namespace App\Repositories\RoomType;

interface RoomTypeRepositoryInterface{
    public function create(array $data);
    public function findById($id);
    public function search(string $keyword);
    public function getAll();
    public function update($id, array $data);
    public function delete($id);
}