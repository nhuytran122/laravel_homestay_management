<?php

namespace App\Enums;

enum BookingExtensionStatus: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case EXPIRED = 'EXPIRED';
    case CANCELLED = 'CANCELLED';
    
}