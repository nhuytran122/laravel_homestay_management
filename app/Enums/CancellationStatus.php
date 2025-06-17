<?php

namespace App\Enums;

enum CancellationStatus: string
{
    case ALLOWED = 'ALLOWED';
    case DENIED_ALREADY_CANCELLED = 'DENIED_ALREADY_CANCELLED';
    case DENIED_ALREADY_COMPLETED = 'DENIED_ALREADY_COMPLETED';
    case DENIED_CHECKIN_TIME_PASSED = 'DENIED_CHECKIN_TIME_PASSED';
    case DENIED_EXPIRED = 'DENIED_EXPIRED';

    public function label(): string
    {
        return match ($this) {
            self::ALLOWED => 'Có thể hủy đơn',
            self::DENIED_ALREADY_CANCELLED => 'Đơn đã bị hủy',
            self::DENIED_ALREADY_COMPLETED => 'Đơn đã hoàn tất',
            self::DENIED_CHECKIN_TIME_PASSED => 'Đã quá thời gian nhận phòng',
            self::DENIED_EXPIRED => 'Đơn đã bị hủy vì quá hạn thanh toán'
        };
    }
}