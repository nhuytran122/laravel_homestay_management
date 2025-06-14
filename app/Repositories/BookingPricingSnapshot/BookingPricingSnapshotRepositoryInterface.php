<?php
namespace App\Repositories\BookingPricingSnapshot;

interface BookingPricingSnapshotRepositoryInterface{
    public function create($data);
    public function findById($id);
    public function getAll();
}