<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;

class VNPayHelper
{
    public static function hmacSHA512(string $key, string $data): string
    {
        if (empty($key) || empty($data)) {
            throw new \InvalidArgumentException("Key hoặc Data không được null");
        }

        return hash_hmac('sha512', $data, $key);
    }

    public static function getIpAddress(): string
    {
        try {
            return Request::ip();
        } catch (\Exception $e) {
            return 'Invalid IP: ' . $e->getMessage();
        }
    }

    public static function getRandomNumber(int $length): string
    {
        $digits = '0123456789';
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= $digits[\random_int(0, strlen($digits) - 1)];
        }
        return $random;
    }

    public static function getPaymentURL(array $paramsMap): string
    {
        ksort($paramsMap);
        $query = [];

        foreach ($paramsMap as $key => $value) {
            if (!is_null($value) && $value !== '') {
                $encodedValue = urlencode($value); 
                $query[] = "{$key}={$encodedValue}";
            }
        }

        return implode('&', $query);
    }

}