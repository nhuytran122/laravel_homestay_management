<?php

namespace App\Services;

use App\Helpers\DiscountHelper;
use App\Models\Booking;
use App\Models\RoomPricing;
use App\Repositories\Booking\BookingRepositoryInterface;
use App\DTOs\BookingPriceDTO;
use App\Enums\BookingStatus;
use App\Enums\RoomPricingType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    private BookingRepositoryInterface $repo;
    private RoomPricingService $roomPricingService;
    private BookingPricingSnapshotService $bookingPricingSnapshotService;
    private RoomStatusHistoryService $roomStatusHistoryService;

    public function __construct(BookingRepositoryInterface $repo, RoomPricingService $roomPricingService,
        BookingPricingSnapshotService $bookingPricingSnapshotService, RoomStatusHistoryService $roomStatusHistoryService)
    {
        $this->repo = $repo;
        $this->roomPricingService = $roomPricingService;
        $this->bookingPricingSnapshotService = $bookingPricingSnapshotService;
        $this->roomStatusHistoryService = $roomStatusHistoryService;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateStatus(Booking $booking, BookingStatus $status): Booking
    {
        $booking->status = $status;
        $booking->save();
        return $booking;
    }

    public function delete($id)
    {
        $this->getById($id); 
        return $this->repo->delete($id);
    }

    public function getById($id): Booking
    {
        $booking = $this->repo->findById($id);

        if (!$booking) {
            throw new ModelNotFoundException("Không tìm thấy đơn đặt phòng với ID: $id");
        }

        return $booking;
    }

    public function handleBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data){
            $booking = $this->repo->create($data);

            $booking->total_amount = $this->calculateTotalAmountBookingRoom($booking);
            $booking->save();

            $dto = $this->getRoomPriceDetail(
                $booking->room->room_type_id,
                $booking->check_in,
                $booking->check_out
            );

            $pricing = $this->roomPricingService->getById($dto->room_pricing_id);
            $this->bookingPricingSnapshotService->create($pricing, $booking->id);

            // Update trạng thái phòng
            $this->roomStatusHistoryService->handleStatusWhenBooking($booking);

            return $booking;
        });
        
    }

    public function calculateTotalAmountBookingRoom(Booking $booking): float
    {
        $raw = $this->calculateRawTotalAmountBookingRoom($booking);
        $customer = $booking->customer;
        $discount = DiscountHelper::calculateDiscountAmount($raw, $customer);

        return $raw - $discount;
    }

    public function calculateRawTotalAmountBookingRoom(Booking $booking): float
    {
        $roomType = $booking->room->room_type;
        $dto = $this->getRoomPriceDetail($roomType->id, $booking->check_in, $booking->check_out);
        $price = $dto->total_price;

        if (str_contains(strtoupper($roomType->name), 'DORM')) {
            $price *= $booking->guest_count;
        }

        return $price;
    }

    public function getRoomPriceDetail($roomTypeId, $checkIn, $checkOut): BookingPriceDTO
    {
        $pricing = $this->roomPricingService->getApplicablePricingForRange($roomTypeId, $checkIn, $checkOut)
            ?? $this->roomPricingService->getDefaultPricingByRoomTypeId($roomTypeId);

        if (!$pricing) {
            return new BookingPriceDTO(0, 0, null, 0, 0, 0);
        }

        return $this->calculateDetail($pricing, $checkIn, $checkOut);
    }

    protected function calculateDetail(RoomPricing $pricing, $checkIn, $checkOut): BookingPriceDTO
    {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);

        $overnightStartTime = Carbon::createFromTime(22, 0);
        $overnightEndTime = Carbon::createFromTime(8, 0);

        $totalMinutes = $checkIn->diffInMinutes($checkOut);
        $totalDays = floor($totalMinutes / 1440);
        $totalPrice = 0;
        $totalNights = 0;
        $extraHours = 0;

        $currentStart = $checkIn->copy();

        // Tính giá theo ngày
        if ($totalMinutes >= 1440) {
            $totalPrice += $pricing->daily_price * $totalDays;
            $currentStart->addDays($totalDays);
        }

        if ($this->isOvernightPeriod($currentStart, $checkOut, $overnightStartTime, $overnightEndTime)) {
            $totalPrice += $pricing->overnight_price;
            $totalNights++;

            $overnightHours = $this->calculateOvernightDuration($currentStart, $checkOut, $overnightStartTime, $overnightEndTime);
            $totalRemainingHours = $currentStart->diffInMinutes($checkOut) / 60;
            $remainingHours = $totalRemainingHours - $overnightHours;

            if ($remainingHours > 0) {
                $totalPrice += $this->calculateHourlyPrice($pricing, $remainingHours, false);
                $extraHours = $remainingHours;
            }
        } else {
            $remainingHours = $currentStart->diffInMinutes($checkOut) / 60;
            $totalPrice += $this->calculateHourlyPrice($pricing, $remainingHours, $totalDays == 0);
            $extraHours = $remainingHours;
        }

        $type = $this->determinePricingType($totalDays, $totalNights, $extraHours);

        return new BookingPriceDTO(
            $pricing->id,
            $totalPrice,
            $type,
            $extraHours,
            $totalDays,
            $totalNights
        );
    }

    protected function calculateOvernightDuration(Carbon $start, Carbon $end, Carbon $overnightStartTime, Carbon $overnightEndTime): float
    {
        $overnightDate = $start->copy()->startOfDay();

        $overnightStart = $overnightDate->copy()->setTimeFrom($overnightStartTime);
        $overnightEnd = $overnightEndTime->lessThan($overnightStartTime)
            ? $overnightDate->copy()->addDay()->setTimeFrom($overnightEndTime)
            : $overnightDate->copy()->setTimeFrom($overnightEndTime);

        $overlapStart = $start->greaterThan($overnightStart) ? $start : $overnightStart;
        $overlapEnd = $end->lessThan($overnightEnd) ? $end : $overnightEnd;

        $minutes = $overlapEnd->diffInMinutes($overlapStart, false);

        return $minutes > 0 ? $minutes / 60 : 0.0;
    }
    protected function isOvernightPeriod(Carbon $start, Carbon $end, Carbon $overnightStartTime, Carbon $overnightEndTime): bool
    {
        $overnightDate = $start->copy()->startOfDay();
        $overnightStart = $overnightDate->copy()->setTimeFrom($overnightStartTime);

        $overnightEnd = $overnightEndTime->lessThan($overnightStartTime)
            ? $overnightDate->copy()->addDay()->setTimeFrom($overnightEndTime)
            : $overnightDate->copy()->setTimeFrom($overnightEndTime);

        $overlapStart = $start->greaterThan($overnightStart) ? $start : $overnightStart;
        $overlapEnd = $end->lessThan($overnightEnd) ? $end : $overnightEnd;

        $hours = $overlapEnd->diffInHours($overlapStart, false);

        return $hours >= 6;
    }

    protected function calculateHourlyPrice(RoomPricing $pricing, float $hours, bool $isFirst): float
    {
        if ($isFirst) {
            return $hours <= $pricing->base_duration
                ? $pricing->base_price
                : $pricing->base_price + ($pricing->extra_hour_price * ($hours - $pricing->base_duration));
        }

        return $pricing->extra_hour_price * $hours;
    }

    private function determinePricingType(int $totalDays, int $totalNights, float $extraHours): RoomPricingType
    {
        if ($totalDays > 0 && ($totalNights > 0 || $extraHours > 0)) {
            return RoomPricingType::MIXED;
        } elseif ($totalDays > 0) {
            return RoomPricingType::DAILY;
        } elseif ($totalNights > 0 && $extraHours > 0) {
            return RoomPricingType::MIXED;
        } elseif ($totalNights > 0) {
            return RoomPricingType::OVERNIGHT;
        } else {
            return RoomPricingType::HOURLY;
        }
    }
}