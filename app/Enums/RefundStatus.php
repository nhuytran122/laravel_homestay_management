<?php

namespace App\Enums;

enum RefundStatus: string
{
    case REQUESTED = 'REQUESTED';        
    case APPROVED = 'APPROVED';          
    case REJECTED = 'REJECTED';         
    case COMPLETED = 'COMPLETED';   
    
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}