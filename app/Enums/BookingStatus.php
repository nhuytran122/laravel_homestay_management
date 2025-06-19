<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING_CONFIRMATION = 'PENDING_CONFIRMATION';
    case PENDING_PAYMENT = 'PENDING_PAYMENT';
    case CONFIRMED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
    case EXPIRED = 'EXPIRED';
    case COMPLETED = 'COMPLETED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_CONFIRMATION => "Đang chờ xác nhận",
            self::PENDING_PAYMENT => "Đang chờ thanh toán",
            self::CONFIRMED => 'Đã xác nhận',
            self::COMPLETED => 'Đã hoàn thành',
            self::CANCELLED => 'Đã hủy',
            self::EXPIRED => "Đã hết hạn"
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}