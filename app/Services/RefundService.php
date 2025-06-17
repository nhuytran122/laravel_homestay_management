<?php
namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\RefundStatus;
use App\Enums\RefundType;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Refund;
use App\Repositories\Refund\RefundRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RefundService {
    private RefundRepositoryInterface $repo;

    public function __construct(RefundRepositoryInterface $repo){
        $this->repo = $repo;
    }

    public function search(?array $filters, int $perPage = 10){
        return $this->repo->search($filters, $perPage);
    }
    
    public function getRefundType(Booking $booking): RefundType
    {
        $checkIn = Carbon::parse($booking->check_in);
        $now = Carbon::now();

        $daysDiff = $now->diffInDays($checkIn, false); 

        if ($daysDiff > 7) {
            return RefundType::FULL;
        } elseif ($daysDiff > 3) {
            return RefundType::PARTIAL_70;
        } else {
            return RefundType::PARTIAL_30;
        }
    }

    public function calculateRefundAmount(Booking $booking): float
    {
        $type = $this->getRefundType($booking);
        $paid = $booking->paid_amount;

        return match ($type) {
            RefundType::FULL => $paid,
            RefundType::PARTIAL_70 => $paid * 0.7,
            RefundType::PARTIAL_30 => $paid * 0.3,
            default => 0.0,
        };
    }

    public function createPendingRefund(Payment $payment, Booking $booking): Refund
    {
        return DB::transaction(function () use ($payment, $booking) {
            $payment->status = PaymentStatus::PENDING_REFUND;
            $payment->save();

            $refund = new Refund();
            $refund->payment_id = $payment->id;
            $refund->refund_amount = $this->calculateRefundAmount($booking);
            $refund->refund_type = $this->getRefundType($booking);
            $refund->status = RefundStatus::REQUESTED;
            $refund->save();
            return $refund;
        });
    }
}