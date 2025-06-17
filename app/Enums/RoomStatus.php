<?php

namespace App\Enums;

enum RoomStatus: string
{
    case BUSY = 'BUSY';
    case CLEANING = 'CLEANING';
    case MAINTENANCE = 'MAINTENANCE';

    public function label(): string
    {
        return match ($this) {
            self::BUSY => 'Đã đặt',
            self::CLEANING => 'Dọn dẹp',
            self::MAINTENANCE => 'Bảo trì'
        };
    }
}