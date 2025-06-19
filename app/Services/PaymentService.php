<?php

namespace App\Services;

use App\Configs\VNPayConfig;
use App\Enums\BookingStatus;
use App\Enums\PaymentPurpose;
use App\Helpers\VNPayHelper;
use App\Models\Payment;
use App\Repositories\Payment\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    private PaymentRepositoryInterface $repo;
    private BookingService $bookingService;
    private BookingExtraServiceService $bookingExtraService;
    private BookingExtensionService $bookingExtensionService;
    private VNPayConfig $vNPayConfig;
    private RoomStatusHistoryService $roomStatusHistoryService;
    private PaymentDetailService $paymentDetailService;

    public function __construct(PaymentRepositoryInterface $repo,
            BookingService $bookingService, BookingExtraServiceService $bookingExtraServiceService, 
            BookingExtensionService $bookingExtensionService, VNPayConfig $vNPayConfig,
            RoomStatusHistoryService $roomStatusHistoryService, PaymentDetailService $paymentDetailService)
    {
        $this->repo = $repo;
        $this->bookingService = $bookingService;
        $this->bookingExtraService = $bookingExtraServiceService;
        $this->bookingExtensionService = $bookingExtensionService;
        $this->vNPayConfig = $vNPayConfig;
        $this->roomStatusHistoryService = $roomStatusHistoryService;
        $this->paymentDetailService = $paymentDetailService;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function getById($id)
    {
        $branch = $this->repo->findById($id);

        if (!$branch) {
            throw new ModelNotFoundException('Không tìm thấy lịch sử thanh toán với ID: ' . $id);
        }
        return $branch;
    }

    public function createVnPayPaymentURL(Request $request, int $bookingId, PaymentPurpose $paymentPurpose): string
    {
        $booking = $this->bookingService->getById($bookingId);
        $amount = 0;

        switch ($paymentPurpose) {
            case PaymentPurpose::ROOM_BOOKING:
                $amount = intval($booking->total_amount * 100);
                break;

            case PaymentPurpose::EXTENDED_HOURS:
                $amount = intval($booking->total_amount * 100);
                break;

            case PaymentPurpose::ADDITIONAL_SERVICE:
                $amount = intval($this->bookingExtraService->calculateUnpaidServicesTotalAmount($bookingId) * 100);
                break;

            default:
                throw new \InvalidArgumentException("Mục đích thanh toán không hợp lệ.");
        }

        $bankCode = $request->input('bankCode');
        $vnpParams = $this->vNPayConfig->getVNPayConfig($bookingId, $paymentPurpose);

        $vnpParams['vnp_Amount'] = (string) $amount;
        if (!empty($bankCode)) {
            $vnpParams['vnp_BankCode'] = $bankCode;
        }

        $vnpParams['vnp_IpAddr'] = $request->ip();

        ksort($vnpParams);

        $hashData = VNPayHelper::getPaymentURL($vnpParams);
        $vnpSecureHash = VNPayHelper::hmacSHA512($this->vNPayConfig->getSecretKey(), $hashData);
        $vnpParams['vnp_SecureHash'] = $vnpSecureHash;
        $queryUrl = VNPayHelper::getPaymentURL($vnpParams);
        
        return $this->vNPayConfig->getVnpPayUrl() . '?' . $queryUrl;
    }

    public function handleSavePaymentWhenCheckout(Payment $payment, PaymentPurpose $paymentPurpose): Payment
    {
        return DB::transaction(function () use ($payment, $paymentPurpose) {
            $payment->save();
            $booking = $payment->booking;

            if ($paymentPurpose === PaymentPurpose::ROOM_BOOKING) {
                $this->bookingService->updateStatus($booking, BookingStatus::CONFIRMED);
            }

            if ($paymentPurpose === PaymentPurpose::EXTENDED_HOURS) {
                $this->bookingService->updateStatus($booking, BookingStatus::CONFIRMED);
                $this->roomStatusHistoryService->handleStatusWhenBookingExtend($booking);
            }

            $oldPaid = $booking->paid_amount ?? 0;
            $booking->paid_amount = $oldPaid + $payment->total_amount;
            $booking->save();

            $this->paymentDetailService->handleSavePaymentDetail($payment, $paymentPurpose);

            return $payment;
        });
    }
}