<?php 
namespace App\Http\Controllers\Client;

use App\Enums\PaymentPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingServicePostConfirmedRequest;
use App\Models\Booking;
use App\Services\BookingExtraServiceService;

class BookingServiceController extends Controller{
    private BookingExtraServiceService $bookingExtraService;

    public function __construct(BookingExtraServiceService $bookingExtraService){
        $this->bookingExtraService = $bookingExtraService;
    }
    public function index(Booking $booking) 
    {
        $bookingId = $booking->id;
        $prepaid = $this->bookingExtraService->getByBookingAndPrepaidType($bookingId, true);
        $postpaid = $this->bookingExtraService->getByBookingAndPrepaidType($bookingId, false);

        return response()->json([
            'prepaid' => $prepaid,
            'postpaid' => $postpaid,
        ]);
    }

    public function unpaidTotal(Booking $booking) {
        $bookingId = $booking->id;
        $total_unpaid_amount = $this->bookingExtraService->calculateUnpaidServicesTotalAmount($bookingId);
        return response()->json([
            'total_unpaid_amount_booking_services' => $total_unpaid_amount
        ]);
    }

    public function canPayPostpaid(Booking $booking) {
        $result = $this->checkCanPayPostpaidServices($booking->id);

        return response()->json([
            'can_pay' => $result['can_pay'],
            'message' => $result['reason'],
        ]);
    }

    public function create(Booking $booking, BookingServicePostConfirmedRequest $request){
        $data = $request->validated();
        $booking_id = $booking->id;
        $services = $data['services'];
        $this->bookingExtraService->saveMultipleBookingServices($services, $booking_id);
        $booking->refresh();
        return response()->json([
            'list_booking_services' => $booking->booking_services,
            'message' => 'Đặt dịch vụ trả sau thành công',
        ], 201);  
    }

    public function handlePayUnpaidBookingServices(Booking $booking){
        $booking_id = $booking->id;
        $result = $this->checkCanPayPostpaidServices($booking_id);

        if (!$result['can_pay']) {
            return response()->json([
                'can_pay' => false,
                'message' => $result['reason'],
            ]);
        }

        $checkoutUrl = route('checkout', [
            'bookingId' => $booking_id,
            'paymentPurpose' => PaymentPurpose::ADDITIONAL_SERVICE
        ]);

        return response()->json([
            'can_pay' => true,
            'next_url' => $checkoutUrl,
        ], 201)->header('Location', $checkoutUrl);
    }

    private function checkCanPayPostpaidServices(int $bookingId): array {
        if (!$this->bookingExtraService->hasPostpaidService($bookingId)) {
            return [
                'can_pay' => false,
                'reason' => 'Đơn đặt phòng không có dịch vụ trả sau để thanh toán'
            ];
        }
        if (!$this->bookingExtraService->allPostpaidServicesHaveQuantity($bookingId)) {
            return [
                'can_pay' => false,
                'reason' => 'Có dịch vụ trả sau chưa có số lượng'
            ];
        }
        return [
            'can_pay' => true,
            'reason' => 'Có thể tiến hành thanh toán dịch vụ trả sau'
        ];
    }



}