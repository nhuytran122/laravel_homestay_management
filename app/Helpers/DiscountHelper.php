<?php

namespace App\Helpers;

use App\Models\Customer;

class DiscountHelper
{
    public static function calculateFinalPrice(float $originalPrice, Customer $customer): float
    {
        $customer_type = $customer->customer_type;

        if (!$customer || !$customer_type) {
            return $originalPrice;
        }

        $discountRate = $customer_type->discount_rate;

        if ($discountRate > 0) {
            return $originalPrice * (1 - $discountRate / 100);
        }

        return $originalPrice;
    }

    
}