<?php

namespace App\Http\Controllers\Client;

use App\Enums\BookingStatus;
use App\Enums\PaymentPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\BookingServiceRequest;
use App\Models\Booking;
use App\Models\BookingService as ModelsBookingService;
use App\Services\BookingExtraServiceService;
use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Services\ExtraServiceService;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    private $bookingService;
    private $extraService;
    private $bookingExtraService;
    public function __construct(BookingService $bookingService, ExtraServiceService $extraService, 
        BookingExtraServiceService $bookingExtraService){
        $this->bookingService = $bookingService;
        $this->extraService = $extraService;
        $this->bookingExtraService = $bookingExtraService;
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
        return response()->json([
            'data' => $booking,
            'message' => 'Tạo mới đơn đặt phòng phòng thành công',
            'next_url' => route('booking.selectService', $booking->id),
        ], 201)->header('Location', route('booking.selectService', $booking->id));  
    }

    public function selectService(Booking $booking){
        $user = Auth::user();
        $customer = $user->customer;
        $customer_id = $customer->id;

        $booking_id = $booking->id;
        if (!$booking_id) {
            return response()->json([
                'message' => 'Yêu cầu không hợp lệ. Vui lòng đặt phòng trước khi thực hiện đặt dịch vụ'
            ], 400);
        }

        $booking = $this->bookingService->getById($booking_id);
        if ($booking->customer->id !== $customer_id) {
            return response()->json([
                'message' => 'Yêu cầu không hợp lệ. Đơn đặt phòng không thuộc quyền truy cập của bạn'
            ], 403);
        }
        
        $list_prepaid_services = $this->extraService->getListServicesByIsPrepaid(true);
        $listNotPrePaidServices = $this->extraService->getListServicesByIsPrepaid(false);

        return response()->json([
            'booking_id'        => $booking_id,
            'prepaid_services' => $list_prepaid_services,
            'non_prepaid_services' => $listNotPrePaidServices, 
            'next_url' => route('booking.confirmService', $booking->id),
        ], 201)->header('Location', route('booking.confirmService', $booking->id));  
    }

    public function postConfirmBookingService(BookingServiceRequest $request, Booking $booking)
    {
        $user = Auth::user();
        $customer = $user->customer;
        $customer_id = $customer->id;

        $booking_id = $booking->id;

        if (!$booking_id) {
            return response()->json([
                'message' => 'Yêu cầu không hợp lệ. Vui lòng đặt phòng trước khi thực hiện đặt dịch vụ'
            ], 400);
        }

        $booking = $this->bookingService->getById($booking_id);
        if ($booking->customer->id !== $customer_id) {
            return response()->json([
                'message' => 'Yêu cầu không hợp lệ. Đơn đặt phòng không thuộc quyền truy cập của bạn'
            ], 403);
        }
        // Nếu đơn đã xử lý trước đó -> xóa dịch vụ cũ
        if ($booking->status !== BookingStatus::PENDING_BOOKING) {
            $this->bookingExtraService->deleteByBookingId($booking_id);        
        }

        $services = $request->input('services', []);
        foreach ($services as $svc) {
            $service = $this->extraService->getById($svc['serviceId']);

            if (!$service || !$service->is_prepaid) {
                return response()->json([
                    'message' => 'Dịch vụ không hợp lệ hoặc không được phép đặt trước',
                ], 422);
            }

            $bookingService = new ModelsBookingService([
                'booking_id' => $booking->id,
                'service_id' => $svc['serviceId'],
                'quantity' => $svc['quantity'],
                'description' => $svc['description'] ?? null,
            ]);
            $this->bookingExtraService->handleSaveBookingServiceExtra($bookingService);
        }
        $this->bookingService->updateStatus($booking, BookingStatus::PENDING_CONFIRMATION);
        
        return response()->json([
            'data' => $booking->id,
            'message' => 'Xác nhận dịch vụ thành công',
            'next_url' => route('booking.getBookingConfirmationPage', $booking->id),
        ], 201)->header('Location', route('booking.getBookingConfirmationPage', $booking->id));  
    }
    
    public function getBookingConfirmationPage(Booking $booking)
    {
        $user = Auth::user();
        $customer = $user->customer;
        $customer_id = $customer->id;

        $booking_id = $booking->id;

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
        if ($booking->status !== BookingStatus::PENDING_CONFIRMATION) {
            return response()->json([
                'message' => 'Yêu cầu không hợp lệ. Đơn đặt phòng không ở trạng thái thích hợp '
            ], 400);
        }
        return response()->json([
            'booking' => $booking,
            'total_amount' => $booking->total_amount,
            'message' => 'Xác nhận dịch vụ thành công, tiến hành thanh toán',
            'next_url' => route('checkout', [
                'bookingId' => $booking->id,
                'paymentPurpose' => PaymentPurpose::ROOM_BOOKING
            ]),
        ], 201)->header('Location', route('checkout', [
            'bookingId' => $booking->id,
            'paymentPurpose' => PaymentPurpose::ROOM_BOOKING
        ]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}