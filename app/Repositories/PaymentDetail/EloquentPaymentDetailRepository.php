<?php
namespace App\Repositories\PaymentDetail;
use App\Models\PaymentDetail;

    class EloquentPaymentDetailRepository implements PaymentDetailRepositoryInterface{
        public function create($data)
        {
            return PaymentDetail::create($data);
        }

    }