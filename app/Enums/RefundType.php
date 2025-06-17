<?php

namespace App\Enums;

enum RefundType: string
{
    case FULL = 'FULL';
    case PARTIAL_70 = 'PARTIAL_70';
    case PARTIAL_30 = 'PARTIAL_30';

    public function label(): string
    {
        return match ($this) {
            self::FULL => 'Hoàn tiền 100%',
            self::PARTIAL_70 => 'Hoàn tiền 70%',
            self::PARTIAL_30 => 'Hoàn tiền 30%',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function description_policy(): string
    {
        return match ($this) {
            self::FULL => 'Nếu hủy phòng trước thời gian check-in 7 ngày hoặc sớm hơn.',
            self::PARTIAL_70 => 'Nếu hủy phòng trước thời gian check-in trong vòng từ 3 đến 7 ngày.',
            self::PARTIAL_30 => '"Nếu hủy phòng trong vòng 3 ngày trước khi check-in.',
        };
    }
}