<?php
namespace App\Repositories\BookingPricingSnapshot;
use App\Models\BookingPricingSnapshot;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\BookingPricingSnapshot\BookingPricingSnapshotRepositoryInterface;

    class EloquentBookingPricingSnapshotRepository extends BaseEloquentRepository implements BookingPricingSnapshotRepositoryInterface{
        public function __construct()
        {
            $this->model = new BookingPricingSnapshot();
        }
    }