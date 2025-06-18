<?php

namespace App\Enums;

enum RoomStatus: string
{
    case BOOKED = 'BOOKED';
    case EXTENDED = 'EXTENDED';
    case CLEANING = 'CLEANING';
    case MAINTENANCE = 'MAINTENANCE';

    public function label(): string
    {
        return match ($this) {
            self::BOOKED => 'Đã đặt',
            self::EXTENDED => "Đã gia hạn",
            self::CLEANING => 'Dọn dẹp',
            self::MAINTENANCE => 'Bảo trì'
        };
    }
}