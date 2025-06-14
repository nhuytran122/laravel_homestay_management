<?php

namespace App\Enums;

enum RoomPricingType: string
{
    case HOURLY = 'HOURLY';
    case OVERNIGHT = 'OVERNIGHT';
    case DAILY = 'DAILY';
    case DEFAULT = 'DEFAULT';
    case MIXED = 'MIXED';
}