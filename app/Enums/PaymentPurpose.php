<?php

namespace App\Enums;

enum PaymentPurpose: string
{
    case ROOM_BOOKING = 'ROOM_BOOKING';
    case PREPAID_SERVICE = 'PREPAID_SERVICE';
    case ADDITIONAL_SERVICE = 'ADDITIONAL_SERVICE';
    case EXTENDED_HOURS= 'EXTENDED_HOURS';

    public function displayName(): string
    {
        return match ($this) {
            self::ROOM_BOOKING => 'Thanh toán tiền phòng ban đầu',
            self::PREPAID_SERVICE => 'Thanh toán dịch vụ ban đầu',
            self::ADDITIONAL_SERVICE => 'Thanh toán dịch vụ phát sinh',
            self::EXTENDED_HOURS => 'Thanh toán giờ thuê thêm'
        };
    }
}