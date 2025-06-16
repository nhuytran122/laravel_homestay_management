<?php

namespace App\Configs;

use App\Enums\PaymentPurpose;
use App\Helpers\VNPayHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class VNPayConfig
{
    public static function getVNPayConfig(int $bookingId, PaymentPurpose $paymentPurpose): array
    {
        $orderInfo = "BOOKING_{$bookingId}_PURPOSE_{$paymentPurpose->value}";
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $expire = $now->copy()->addMinutes(15);

        return [
            'vnp_Version'    => Config::get('vnpay.version'),
            'vnp_Command'    => Config::get('vnpay.command'),
            'vnp_TmnCode'    => Config::get('vnpay.tmn_code'),
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => VNPayHelper::getRandomNumber(8),
            'vnp_OrderInfo'  => $orderInfo,
            'vnp_OrderType'  => Config::get('vnpay.order_type'),
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => Config::get('vnpay.return_url'),
            'vnp_CreateDate' => $now->format('YmdHis'),
            'vnp_ExpireDate' => $expire->format('YmdHis'),
        ];
    }

    public static function getVnpPayUrl(): string
    {
        return Config::get('vnpay.url_pay');
    }

    public static function getSecretKey(): string
    {
        return Config::get('vnpay.secret_key');
    }
}