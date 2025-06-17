<?php
namespace App\Repositories\Payment;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Booking;
use Illuminate\Support\Collection;

interface PaymentRepositoryInterface{
    public function findById($id);
    public function search(?PaymentType $paymentType = null, ?PaymentStatus $status = null);
    public function getAll();
    public function getCompletedPaymentsByBooking(Booking $booking): Collection;
}