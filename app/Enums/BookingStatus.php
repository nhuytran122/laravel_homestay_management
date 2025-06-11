<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
    case COMPLETED = 'COMPLETED';

    public function displayName(): string
    {
        return match ($this) {
            self::PENDING => 'Đang chờ',
            self::CONFIRMED => 'Đã xác nhận',
            self::COMPLETED => 'Đã hoàn thành',
            self::CANCELLED => 'Đã hủy',
        };
    }
}