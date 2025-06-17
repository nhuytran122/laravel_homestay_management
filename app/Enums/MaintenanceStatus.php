<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case PENDING = 'PENDING';
    case IN_PROGRESS = 'IN_PROGRESS';
    case CANCELLED = 'CANCELLED';
    case COMPLETED = 'COMPLETED';
    case ON_HOLD = 'ON_HOLD';

    public function l(): string
    {
        return match ($this) {
            self::PENDING => 'Đang chờ',
            self::IN_PROGRESS => 'Đang thực hiện',
            self::COMPLETED => 'Đã hoàn thành',
            self::CANCELLED => 'Đã hủy',
            self::ON_HOLD => 'Tạm hoãn'
        };
    }
}