<?php
namespace App\Repositories\Payment;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Payment;

    class EloquentPaymentRepository implements PaymentRepositoryInterface{
        public function findById($id)
        {
            return Payment::find($id); 
        }

        public function search(?PaymentType $paymentType = null, ?PaymentStatus $status = null)
        {
            return Payment::query()
                ->when($paymentType, fn ($q) => $q->where('payment_type', $paymentType->value))
                ->when($status, fn ($q) => $q->where('status', $status->value))
                ->orderByDesc('payment_date')
                ->get();
        }

        public function getAll()
        {
            return Payment::all();
        }

        public function create($data)
        {
            return Payment::create($data);
        }

    }