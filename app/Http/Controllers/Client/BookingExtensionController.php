<?php

namespace App\Http\Controllers\Client;

use App\Enums\BookingStatus;
use App\Enums\PaymentPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingExtensionRequest;
use App\Models\Booking;
use App\Services\BookingExtensionService;

class BookingExtensionController extends Controller
{
    private BookingExtensionService $bookingExtensionService;

    public function __construct(BookingExtensionService $bookingExtensionService){
        $this->bookingExtensionService = $bookingExtensionService;
    }
    
    public function checkCanBookingExtension(Booking $booking){
        $status = $booking->status;
        if($status === BookingStatus::PENDING_BOOKING_SERVICE || $status === BookingStatus::PENDING_CONFIRMATION ||
            $status === BookingStatus::PENDING_PAYMENT){
                return response()->json([
                    'message' => 'Vui lòng hoàn tất đặt phòng và thanh toán trước khi thực hiện yêu cầu này'
                ]);
        }
        if($status === BookingStatus::CANCELLED || $status === BookingStatus::EXPIRED){
            return response()->json([
                'message' => 'Đơn đặt phòng đã bị hủy, không thể thực hiện được yêu cầu'
            ]);
        }
        if($status === BookingStatus::COMPLETED){
            return response()->json([
                'message' => 'Đơn đặt phòng đã hoàn tất, không thể thực hiện yêu cầu'
            ]);
        }
        return response()->json([
            'message' => 'Có thể đặt gia hạn'
        ]);
    }

    public function create(Booking $booking, BookingExtensionRequest $request){
        $data = $request->validated();
        $new_booking_extension = [
            'booking_id' => $booking->id,
            'extended_hours' => $data['extended_hours'],
        ];
        $created_data = $this->bookingExtensionService->create($new_booking_extension);
        $booking_id = $created_data->booking->id;
        return response()->json([
                'booking_extension' => $created_data,
                'message' => 'Tạo gia hạn cư trú thành công, tiến hành thanh toán','next_url' => route('checkout', [
                'booking' => $booking,
                'paymentPurpose' => PaymentPurpose::EXTENDED_HOURS,
            ]),
        ], 201)->header('Location', route('checkout', [
            'booking' => $booking,
            'paymentPurpose' => PaymentPurpose::EXTENDED_HOURS
        ]));
    }
}