<?php
    
namespace App\Services;

use App\Models\BookingPricingSnapshot;
use App\Models\RoomPricing;
use App\Repositories\BookingPricingSnapshot\BookingPricingSnapshotRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookingPricingSnapshotService
{
    private BookingPricingSnapshotRepositoryInterface $repo;

    public function __construct(BookingPricingSnapshotRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function create(RoomPricing $pricing, int $booking_id)
    {
        $data = array_merge(
            ['booking_id' => $booking_id],
            $pricing->only([
                'base_duration',
                'base_price',
                'extra_hour_price',
                'overnight_price',
                'daily_price',
            ])
        );

        return $this->repo->create($data);
    }


    public function getById($id)
    {
        $booking_pricing_snapshot = $this->repo->findById($id);

        if (!$booking_pricing_snapshot) {
            throw new ModelNotFoundException('Không tìm thấy lịch sử giá đơn đặt phòng với ID: ' . $id);
        }
        return $booking_pricing_snapshot;
    }
}