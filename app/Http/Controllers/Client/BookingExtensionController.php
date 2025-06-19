<?php

namespace App\Http\Controllers\Client;

use App\Enums\BookingStatus;
use App\Enums\PaymentPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingExtensionRequest;
use App\Models\Booking;
use App\Models\BookingExtension;
use App\Services\BookingExtensionService;
use App\Services\RoomStatusHistoryService;

class BookingExtensionController extends Controller
{
    private BookingExtensionService $bookingExtensionService;
    private RoomStatusHistoryService $roomStatusHistoryService;

    public function __construct(BookingExtensionService $bookingExtensionService, RoomStatusHistoryService $roomStatusHistoryService){
        $this->bookingExtensionService = $bookingExtensionService;
        $this->roomStatusHistoryService = $roomStatusHistoryService;
    }
    
    public function checkCanBookingExtension(Booking $booking){
        $status = $booking->status;
        $booking_id = $booking->id;
        if($status === BookingStatus::PENDING_CONFIRMATION ||
            $status === BookingStatus::PENDING_PAYMENT){
                return response()->json([
                    'message' => 'Vui lòng hoàn tất đặt phòng và thanh toán trước khi thực hiện yêu cầu này'
                ], 400);
        }
        if($status === BookingStatus::CANCELLED || $status === BookingStatus::EXPIRED){
            return response()->json([
                'message' => 'Đơn đặt phòng đã bị hủy, không thể thực hiện được yêu cầu'
            ], 400);
        }
        if($status === BookingStatus::COMPLETED){
            return response()->json([
                'message' => 'Đơn đặt phòng đã hoàn tất, không thể thực hiện yêu cầu'
            ], 400);
        }

        if($this->bookingExtensionService->hasUnpaidExtensionByBookingId($booking_id)){
            return response()->json([
                'message' => 'Đã có yêu cầu gia hạn đang chờ'
            ], 400);
        }
        return response()->json([
            'message' => 'Có thể đặt gia hạn'
        ], 200);
    }

    public function create(Booking $booking, BookingExtensionRequest $request){
        $response = $this->checkCanBookingExtension($booking);
        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        $data = $request->validated();
        // $new_booking_extension = [
        //     'room_id' => $booking->room_id,
        //     'check_in' => $booking->check_out,
        //     'check_out' => $data['new_checkout'],
        //     'guest_count' => $booking->guest_count,
        //     'customer_id' => $booking->customer_id,
        //     'parent_id' => $booking->id,
        // ];
        // $created_data = $this->bookingExtensionService->create($new_booking_extension);

        $created_data = $this->bookingExtensionService->create(
            $request->toExtensionPayload()
        );
        
        return response()->json([
            'booking_extension' => $created_data,
            'message' => 'Tạo gia hạn cư trú thành công, tiến hành thanh toán',
            'next_url' => route('checkout', [
                'booking' => $created_data,
                'paymentPurpose' => PaymentPurpose::EXTENDED_HOURS,
            ]),
        ], 201)->header('Location', route('checkout', [
            'booking' => $created_data,
            'paymentPurpose' => PaymentPurpose::EXTENDED_HOURS,
        ]));
    }

    public function canPay(Booking $booking, Booking $bookingExtension) {
        $isOverlapping = $this->roomStatusHistoryService->isOverlappingRoomWithExtension($bookingExtension->booking, $bookingExtension->check_out);
        if($isOverlapping){
            return response()->json([
                'message' => 'Khoảng thời gian bạn yêu cầu gia hạn hiện không còn khả dụng do phòng đã được đặt bởi khách khác. Vui lòng chọn thời gian khác.'
            ], 400);
        }
        else{
            return response()->json([
                'message' => 'Có thể thanh toán'
            ]);
        }
    }



}