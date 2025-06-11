<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
    case REFUNDED = 'REFUNDED';
    case PENDING_REFUND = 'PENDING_REFUND';

    public function displayName(): string
    {
        return match ($this) {
            self::PENDING => 'Đang chờ',
            self::COMPLETED => 'Đã hoàn thành',
            self::FAILED => 'Thất bại',
            self::REFUNDED => 'Đã hoàn tiền',
            self::PENDING_REFUND => 'Đang chờ hoàn tiền'
        };
    }
}