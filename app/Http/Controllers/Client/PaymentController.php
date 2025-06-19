<?php

namespace App\Http\Controllers\Client;

use App\Enums\PaymentPurpose;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingExtensionService;
use App\Services\BookingService;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    private PaymentService $paymentService;
    private BookingService $bookingService;
    private BookingExtensionService $bookingExtensionService;

    public function __construct(PaymentService $paymentService, BookingService $bookingService, BookingExtensionService $bookingExtensionService)
    {
        $this->paymentService = $paymentService;
        $this->bookingService = $bookingService;
        $this->bookingExtensionService = $bookingExtensionService;
    }
    
    public function handlePay(Booking $booking, Request $request)
    {
        $booking_id = $booking->id;
        
        $purposeValue = $request->query('paymentPurpose');

        try {
            $paymentPurpose = PaymentPurpose::from($purposeValue);
        } catch (\ValueError $e) {
            return response()->json([
                'message' => 'Mục đích thanh toán không hợp lệ',
            ], 400);
        }

        switch ($paymentPurpose) {
            case PaymentPurpose::ROOM_BOOKING:
                $this->authorize('payRoom', $booking);
                break;
            case PaymentPurpose::EXTENDED_HOURS:
                $this->authorize('payRoom', $booking);
                break;
            case PaymentPurpose::ADDITIONAL_SERVICE:
                $this->authorize('bookAndPayAdditionalBooking', $booking);
                break;
            case PaymentPurpose::PREPAID_SERVICE:
                $this->authorize('bookAndPayAdditionalBooking', $booking);
                break;

            default:
                abort(400, 'Mục đích thanh toán không hợp lệ');
        }

        $paymentUrl = $this->paymentService->createVnPayPaymentURL($request, $booking_id, $paymentPurpose);

        return response()->json([
            'payment_url' => $paymentUrl,
            'message' => 'Thực hiện thanh toán',
        ]);
    }

    public function handleVnPayCallback(Request $request)
    {
        $status = $request->query('vnp_ResponseCode');
        $orderInfo = $request->query('vnp_OrderInfo');

        [$bookingPart, $purposePart] = explode('_PURPOSE_', $orderInfo);
        $bookingId = (int) str_replace('BOOKING_', '', $bookingPart);
        $paymentPurpose = PaymentPurpose::from($purposePart); 

        if ($status === '00') {
            $payment = new Payment();
            $payment->payment_type = PaymentType::TRANSFER;
            $payment->status = PaymentStatus::COMPLETED;

            $vnpTxnRef = $request->query('vnp_TxnRef');
            $vnpPayDate = $request->query('vnp_PayDate');
            $vnpTransactionNo = $request->query('vnp_TransactionNo');

            $payment->vnp_txn_ref = $vnpTxnRef;
            $payment->payment_date = Carbon::createFromFormat('YmdHis', $vnpPayDate);
            $payment->vnp_transaction_no = $vnpTransactionNo;

            $booking = $this->bookingService->getById($bookingId);
            $payment->booking_id = $bookingId;

            $vnpAmountStr = $request->query('vnp_Amount');
            if (!$vnpAmountStr) {
                return response()->json([
                    'message' => 'vnp_Amount không tồn tại'
                ], 400);
            }
            $payment->total_amount = (float) $vnpAmountStr / 100;

            $this->paymentService->handleSavePaymentWhenCheckout($payment, $paymentPurpose);
            $booking = $booking->refresh();
            return response()->json([
                'booking' => $booking,
                'message' => 'Xác nhận và thanh toán thành công',
                'next_url' => route('booking.show', [
                    'booking' => $booking,
                ]),
            ], 201)->header('Location', route('booking.show', [
                'booking' => $booking,
                ]));
        }
        return response()->json([
            'message' => 'Giao dịch thất bại'
        ]);
    }
}