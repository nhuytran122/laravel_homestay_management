<?php

namespace App\Http\Controllers\Client;

use App\Enums\BookingStatus;
use App\Enums\CancellationStatus;
use App\Enums\PaymentPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\BookingServiceInitialRequest;
use App\Models\Booking;
use App\Services\BookingExtraServiceService;
use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Services\ExtraServiceService;
use App\Services\RefundService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    private BookingService $bookingService;
    private ExtraServiceService $extraService;
    private BookingExtraServiceService $bookingExtraService;
    private RefundService $refundService;
    public function __construct(BookingService $bookingService, ExtraServiceService $extraService, 
        BookingExtraServiceService $bookingExtraService, RefundService $refundService){
        $this->bookingService = $bookingService;
        $this->extraService = $extraService;
        $this->bookingExtraService = $bookingExtraService;
        $this->refundService = $refundService;
    }
    
    public function index()
    {
        //
    }

    public function handleBooking(BookingRequest $request){
        $user = Auth::user();
        $customer = $user->customer;
        $customer_id = $customer->id;

        $data = $request->validated();
        $data['customer_id'] = $customer_id;

        $booking = $this->bookingService->handleBooking($data);
        $booking_id = $booking->id;
        return response()->json([
            'data' => $booking,
            'message' => 'Tạo mới đơn đặt phòng phòng thành công',
            'next_url' => route('booking.selectService', $booking),
        ], 201)->header('Location', route('booking.selectService', $booking));  
    }

    public function selectService(Booking $booking){
        $booking_id = $booking->id;
        
        $list_prepaid_services = $this->extraService->getListServicesByIsPrepaid(true);
        $listNotPrePaidServices = $this->extraService->getListServicesByIsPrepaid(false);

        return response()->json([
            'booking_id'        => $booking_id,
            'prepaid_services' => $list_prepaid_services,
            'non_prepaid_services' => $listNotPrePaidServices, 
            'next_url' => route('booking.confirmService', $booking_id),
        ], 201)->header('Location', route('booking.confirmService', $booking_id));  
    }

    public function postConfirmBookingService(BookingServiceInitialRequest $request, Booking $booking)
    {
        $booking_id = $booking->id;
        // Nếu đơn đã xử lý trước đó -> xóa dịch vụ cũ
        $data = $request->validated();
        if ($booking->status !== BookingStatus::PENDING_BOOKING_SERVICE) {
            $this->bookingExtraService->deleteByBookingId($booking_id);        
        }

        $services = $data['services'];
        $this->bookingExtraService->saveMultipleBookingServices($services, $booking_id);
        
        $this->bookingService->updateStatus($booking, BookingStatus::PENDING_CONFIRMATION);
        return response()->json([
            'data' => $booking_id,
            'message' => 'Xác nhận dịch vụ thành công',
            'next_url' => route('booking.getBookingConfirmationPage', $booking_id),
        ], 201)->header('Location', route('booking.getBookingConfirmationPage', $booking_id));  
    }
    
    public function getBookingConfirmationPage(Booking $booking)
    {
        if ($booking->status !== BookingStatus::PENDING_CONFIRMATION) {
            return response()->json([
                'message' => 'Yêu cầu không hợp lệ. Đơn đặt phòng không ở trạng thái thích hợp'
            ], 400);
        }
        $this->bookingService->updateStatus($booking, BookingStatus::PENDING_PAYMENT);
        return response()->json([
            'booking' => $booking,
            'total_amount' => $booking->total_amount,
            'message' => 'Xác nhận dịch vụ thành công, tiến hành thanh toán',
            'next_url' => route('checkout', [
                'booking' => $booking,
                'paymentPurpose' => PaymentPurpose::ROOM_BOOKING
            ]),
        ], 201)->header('Location', route('checkout', [
            'booking' => $booking,
            'paymentPurpose' => PaymentPurpose::ROOM_BOOKING
        ]));
    }
    
    public function bookingHistory(Request $request)
    {
        $customerId = Auth::user()->customer->id;
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'status' => Rule::in(BookingStatus::values())
        ]);
        $filters = [
            'from' => $request->input('from_date'),
            'to'   => $request->input('to_date'),
            'status' => $request->input('status')
        ];

        $bookings = $this->bookingService->searchBookingsByCustomer($customerId, $filters, 10);

        return response()->json([
            'bookings' => $bookings,
            'filters' => $filters,
            'statuses' => BookingStatus::cases(),
        ]);
    }

    public function show(Booking $booking){
        return response()->json([
            'booking' => $booking,
        ]);
    }

    public function checkCancelability(Booking $booking)
    {
        $cancellation_status = $this->bookingService->checkCancelability($booking->id);
        return response()->json([
            'status' => $cancellation_status->value,
            'message' => $cancellation_status->label()
        ]);
    }

    public function checkRefund(Booking $booking){
        if($booking->status !== BookingStatus::CONFIRMED){
            return response()->json([
                'message' => 'Đơn đặt phòng chưa xác nhận không đủ điều kiện để hoàn tiền'
            ], 400);
        }
        $refund_type = $this->refundService->getRefundType($booking);
        $refund_amount = $this->refundService->calculateRefundAmount($booking);
        return response()->json([
            'refund_type' => $refund_type->label(),
            'refund_amount' => $refund_amount
        ]);
    }

    public function cancelBooking(Booking $booking){
        $booking_id = $booking->id;
        $cancellation_status = $this->bookingService->checkCancelability($booking_id);
        if($cancellation_status !== CancellationStatus::ALLOWED){
            return response()->json([
                'message' => 'Không thể hủy đơn, ' . $cancellation_status->label()
            ]);
        }
        $booking = $this->bookingService->cancelBooking($booking_id);
        return response()->json([
            'booking' => $booking,
            'message' => 'Hủy đơn đặt phòng thành công'
        ]);
    }
    
}