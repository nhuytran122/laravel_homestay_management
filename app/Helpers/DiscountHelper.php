<?php

namespace App\Helpers;

use App\Models\Customer;

class DiscountHelper
{
    public static function calculateDiscountAmount(float $originalPrice, Customer $customer): float
    {
        $customer_type = $customer->customer_type;
        if (!$customer || !$customer_type) {
            return 0.0;
        }

        $discountRate = $customer_type->discount_rate;
        return ($discountRate > 0) ? $originalPrice * $discountRate / 100 : 0.0;
    }
}