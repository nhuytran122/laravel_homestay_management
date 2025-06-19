<?php

namespace App\Services;

use App\Models\Payment;
use App\Enums\PaymentPurpose;
use App\Helpers\DiscountHelper;
use App\Repositories\PaymentDetail\PaymentDetailRepositoryInterface;
use App\Services\BookingExtensionService;

class PaymentDetailService
{
    private PaymentDetailRepositoryInterface $repo;
    private BookingExtraServiceService $bookingExtraService;
    private BookingExtensionService $bookingExtensionService;
    private BookingService $bookingService;

    public function __construct(
        PaymentDetailRepositoryInterface $repo,
        BookingExtraServiceService $bookingExtraService,
        BookingExtensionService $bookingExtensionService,
        BookingService $bookingService
    ) {
        $this->repo = $repo;
        $this->bookingExtraService = $bookingExtraService;
        $this->bookingExtensionService = $bookingExtensionService;
        $this->bookingService = $bookingService;
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function handleSavePaymentDetail(Payment $payment, PaymentPurpose $paymentPurpose): void
    {
        $bookingId = $payment->booking->id;

        if ($paymentPurpose === PaymentPurpose::ROOM_BOOKING) {
            $this->handleRoomBookingPayment($payment, $bookingId);
        } elseif ($paymentPurpose === PaymentPurpose::ADDITIONAL_SERVICE) {
            $this->handleAdditionalServicePayment($payment, $bookingId);
        } elseif ($paymentPurpose === PaymentPurpose::EXTENDED_HOURS) {
            $this->handleExtendedHoursPayment($payment, $bookingId);
        }
    }

    private function handleRoomBookingPayment(Payment $payment, int $bookingId): void
    {
        $booking = $payment->booking;

        $this->create([
            'payment_id' => $payment->id,
            'payment_purpose' => PaymentPurpose::ROOM_BOOKING,
            'base_amount' => $this->bookingService->calculateRawTotalAmountBookingRoom($booking),
            'final_amount' => $this->bookingService->calculateTotalAmountBookingRoom($booking),
        ]);

        if ($this->bookingExtraService->existsByBookingId($bookingId)) {
            $services = $this->bookingExtraService->findBookingServicesWithoutPaymentDetailByBookingId($bookingId);

            foreach ($services as $bService) {
                if ($bService->service->is_prepaid) {
                    $raw = $bService->service->price * $bService->quantity;
                    $final = DiscountHelper::calculateFinalPrice($raw, $booking->customer);

                    $this->create([
                        'payment_id' => $payment->id,
                        'payment_purpose' => PaymentPurpose::PREPAID_SERVICE,
                        'booking_service_id' => $bService->id,
                        'base_amount' => $raw,
                        'final_amount' => $final,
                    ]);
                }
            }
        }
    }

    private function handleAdditionalServicePayment(Payment $payment, int $bookingId): void
    {
        if ($this->bookingExtraService->existsByBookingId($bookingId)) {
            $services = $this->bookingExtraService->findBookingServicesWithoutPaymentDetailByBookingId($bookingId);

            foreach ($services as $bService) {
                $raw = $bService->raw_total_amount;
                $final = DiscountHelper::calculateFinalPrice($raw, $payment->booking->customer);

                $this->create([
                    'payment_id' => $payment->id,
                    'payment_purpose' => PaymentPurpose::ADDITIONAL_SERVICE,
                    'booking_service_id' => $bService->id,
                    'base_amount' => $raw,
                    'final_amount' => $final,
                ]);
            }
        }
    }

    private function handleExtendedHoursPayment(Payment $payment, int $bookingId): void
    {
        $bookingExtension = $payment->booking;
        $raw = $this->bookingService->calculateTotalAmountBookingRoom($bookingExtension);
        $final = DiscountHelper::calculateFinalPrice($raw, $bookingExtension->customer);

        $this->create([
            'payment_id' => $payment->id,
            'payment_purpose' => PaymentPurpose::EXTENDED_HOURS,
            'base_amount' => $raw,
            'final_amount' => $final,
        ]);
    }
}