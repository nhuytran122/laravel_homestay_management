<?php
namespace App\Repositories\Payment;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;

interface PaymentRepositoryInterface{
    public function create(array $data);
    public function findById($id);
    public function search(?PaymentType $paymentType = null, ?PaymentStatus $status = null);
    public function getAll();
}