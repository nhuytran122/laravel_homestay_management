<?php

namespace App\Http\Controllers\Client;

use App\Enums\PaymentPurpose;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\BookingExtensionService;
use App\Services\BookingService;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

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
    
    public function handlePay(Request $request)
    {
        $booking_id = $request->query('bookingId');
        $user = Auth::user();
        $customer = $user->customer;
        $customer_id = $customer->id;

        if (!$booking_id) {
            return response()->json([
                'message' => 'Yêu cầu không hợp lệ. Vui lòng đặt phòng trước khi thực hiện xác nhận'
            ], 400);
        }

        $booking = $this->bookingService->getById($booking_id);
        if ($booking->customer->id !== $customer_id) {
            return response()->json([
                'message' => 'Yêu cầu không hợp lệ. Đơn đặt phòng không thuộc quyền truy cập của bạn'
            ], 403);
        }
        
        $purposeValue = $request->query('paymentPurpose');

        try {
            $paymentPurpose = PaymentPurpose::from($purposeValue);
        } catch (\ValueError $e) {
            return response()->json([
                'message' => 'Mục đích thanh toán không hợp lệ',
            ], 400);
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
            $payment->booking_id = $booking->id;

            $vnpAmountStr = $request->query('vnp_Amount');
            if (!$vnpAmountStr) {
                abort(400, 'vnp_Amount không tồn tại');
            }
            $payment->total_amount = (float) $vnpAmountStr / 100;

            $this->paymentService->handleSavePaymentWhenCheckout($payment, $paymentPurpose);

            return Redirect::to("/booking/booking-history/$bookingId");
        }

        if ($status === '24' && $paymentPurpose === PaymentPurpose::EXTENDED_HOURS) {
            $this->bookingExtensionService->deleteLatestExtensionByBookingId($bookingId);
            return Redirect::to('/checkout/payment-failed')->with('errorMessage', 'Giao dịch của bạn đã bị hủy');
        }

        return Redirect::to('/checkout/payment-failed');
    }
}