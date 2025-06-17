<?php

namespace App\Enums;

enum TransactionType: string
{
    case IMPORT = 'IMPORT';
    case EXPORT = 'EXPORT';

    public function label(): string
    {
        return match ($this) {
            self::IMPORT => 'Nhập kho',
            self::EXPORT => 'Xuất kho',
        };
    }
}