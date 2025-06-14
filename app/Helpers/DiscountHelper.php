<?php

namespace App\Helpers;

use App\Models\Customer;

class DiscountHelper
{
    public static function calculateDiscountAmount(float $originalPrice, Customer $customer): float
    {
        if (!$customer || !$customer->customerType) {
            return 0.0;
        }

        $discountRate = $customer->customerType->discount_rate;
        return ($discountRate > 0) ? $originalPrice * $discountRate / 100 : 0.0;
    }
}