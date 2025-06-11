<?php

namespace App\Enums;

enum PaymentType: string
{
    case CASH = 'CASH';
    case TRANSFER = 'TRANSFER';

    public function displayName(): string
    {
        return match ($this) {
            self::CASH => 'Tiền mặt',
            self::TRANSFER => 'Chuyển khoản',
        };
    }
}