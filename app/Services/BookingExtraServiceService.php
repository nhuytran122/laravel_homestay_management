<?php

namespace App\Services;

use App\Enums\BookingServiceStatus;
use App\Helpers\DiscountHelper;
use App\Models\BookingService;
use App\Repositories\BookingService\BookingServiceRepositoryInterface;
use App\Services\BookingService as ServicesBookingService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class BookingExtraServiceService
{
    private BookingServiceRepositoryInterface $repo;
    private ServicesBookingService $bookingService;

    public function __construct(BookingServiceRepositoryInterface $repo, ServicesBookingService $bookingService)
    {
        $this->repo = $repo;
        $this->bookingService = $bookingService;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function delete($id)
    {
        $this->getById($id); 
        return $this->repo->delete($id);
    }

    public function getById($id): BookingService
    {
        $booking = $this->repo->findById($id);

        if (!$booking) {
            throw new ModelNotFoundException("Không tìm thấy đơn đặt dịch vụ với ID: $id");
        }
        return $booking;
    }

    public function handleSaveBookingServiceExtra(BookingService $bService): BookingService
    {
        return DB::transaction(function () use ($bService) {
            // Lấy booking hiện tại
            $currentBooking = $this->bookingService->getById($bService->booking_id);
            $customer = $currentBooking->customer;

            // Kiểm tra nếu là dịch vụ trả trước or có số lượng
            if ($bService->service->is_prepaid || $bService->quantity !== null) {
                $rawAmount = $bService->getRawTotalAmount();
                $finalAmount = DiscountHelper::calculateFinalPrice($rawAmount, $customer);

                // Cập nhật tổng tiền booking
                $currentBooking->total_amount += $finalAmount;
                $currentBooking->save();
            }
            $bService->save();

            return $bService;
        });
    }

    public function findByBookingId(int $bookingId)
    {
        return $this->repo->findByBookingId($bookingId);
    }

    public function deleteByBookingId(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId) {
            $bookingServices = $this->findByBookingId($bookingId);

            foreach ($bookingServices as $bService) {
                $this->deleteById($bService->id);
            }
        });
    }

    public function deleteById(int $bookingServiceId): void
    {
        $bService = BookingService::with('booking.customer')->findOrFail($bookingServiceId);

        DB::transaction(function () use ($bService, $bookingServiceId) {
            if (!is_null($bService->quantity)) {
                $booking = $bService->booking;
                $customer = $booking->customer;

                $rawTotal = $bService->getRawTotalAmount();

                $oldTotalPriceService = DiscountHelper::calculateFinalPrice($rawTotal, $customer);

                $booking->total_amount -= $oldTotalPriceService;
                $booking->save();
            }

            if ($this->canUpdateAndDelete($bService)) {
                $this->delete($bookingServiceId);
            }
        });
    }

    private function canUpdateAndDelete(BookingService $bService): bool
    {
        $hasPaid = $bService->payment_detail()->exists();
        return !$hasPaid;
    }

    public function findBookingServicesWithoutPaymentDetailByBookingId($bookingId)
    {
        return $this->repo->findBookingServicesWithoutPaymentDetailByBookingId($bookingId);
    }

    public function calculateUnpaidServicesTotalAmount(int $bookingId): float
    {
        $unpaidServices = $this->findBookingServicesWithoutPaymentDetailByBookingId($bookingId);

        if ($unpaidServices->isEmpty()) {
            return 0.0;
        }

        $customer = $unpaidServices->first()->booking->customer;
        $totalAmount = 0.0;

        foreach ($unpaidServices as $bookingService) {
            if (!is_null($bookingService->quantity)) {
                $totalAmount += $bookingService->getRawTotalAmount();
            }
        }
        return DiscountHelper::calculateFinalPrice($totalAmount, $customer);
    }

    public function existsByBookingId(int $bookingId){
        return $this->repo->existsByBookingId($bookingId);
    }

    public function getByBookingAndPrepaidType($bookingId, bool $isPrepaid){
        return $this->repo->getByBookingAndPrepaidType($bookingId, $isPrepaid);
    }

    public function hasPostpaidService(int $bookingId){
        return $this->repo->hasPostpaidService($bookingId);
    }

    public function allPostpaidServicesHaveQuantity(int $bookingId){
        return !$this->repo->existsPostpaidServiceWithoutQuantity($bookingId);
    }

    public function bulkUpdateServiceStatusByBookingId(int $bookingId, BookingServiceStatus $status){
        return $this->repo->bulkUpdateServiceStatusByBookingId($bookingId, $status);
    }

    public function saveMultipleBookingServices(array $services, int $bookingId)
    {
        DB::transaction(function() use ($services, $bookingId){
            foreach ($services as $svc) {
                $bookingService = new BookingService([
                    'booking_id' => $bookingId,
                    'service_id' => $svc['serviceId'],
                    'quantity' => $svc['quantity'] ?? null,
                    'description' => $svc['description'] ?? null,
                ]);
                $this->handleSaveBookingServiceExtra($bookingService);
            }
        });
    }

}