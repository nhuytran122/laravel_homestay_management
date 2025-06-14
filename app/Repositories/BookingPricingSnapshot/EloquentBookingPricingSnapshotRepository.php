<?php
namespace App\Repositories\BookingPricingSnapshot;
use App\Models\BookingPricingSnapshot;
use App\Repositories\BookingPricingSnapshot\BookingPricingSnapshotRepositoryInterface;

    class EloquentBookingPricingSnapshotRepository implements BookingPricingSnapshotRepositoryInterface{
        public function findById($id)
        {
            return BookingPricingSnapshot::find($id); 
        }

        public function getAll()
        {
            return BookingPricingSnapshot::all();
        }
        
        public function create($data)
        {
            return BookingPricingSnapshot::create($data);
        }
    }