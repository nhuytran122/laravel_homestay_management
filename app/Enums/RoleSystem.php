<?php

namespace App\Enums;

enum RoleSystem: string
{
    case MANAGER = 'MANAGER';
    case HOUSEKEEPER = 'HOUSEKEEPER';
    case STAFF = 'STAFF';
    case CUSTOMER = 'CUSTOMER';

    public function label(): string
    {
        return match ($this) {
            self::MANAGER => 'Quản lý',
            self::HOUSEKEEPER => 'Nhân viên dọn dẹp',
            self::STAFF => 'Nhân viên',
            self::CUSTOMER => 'Khách hàng'
        };
    }
}