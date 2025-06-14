<?php
namespace App\DTOs;
use App\Enums\RoomPricingType;

    class BookingPriceDTO
    {
        public function __construct(
            public int $room_pricing_id,
            public float $total_price,
            public ?RoomPricingType $pricing_type,
            public float $extra_hours,
            public float $total_days,
            public float $total_nights,
        ) {}
    }