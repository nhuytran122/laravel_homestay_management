<?php

namespace App\Enums;

enum RoomStatus: string
{
    case BUSY = 'BUSY';
    case CLEANING = 'CLEANING';

    public function displayName(): string
    {
        return match ($this) {
            self::BUSY => 'Đã đặt',
            self::CLEANING => 'Dọn dẹp',
        };
    }
}