<?php
namespace App\Repositories\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\Booking\BookingRepositoryInterface;

    class EloquentBookingRepository extends BaseEloquentRepository implements BookingRepositoryInterface{
        public function __construct()
        {
            $this->model = new Booking();
        }
        
        public function searchBookingsByCustomer(int $customerId, ?array $filters, int $perPage = 10)
        {
            $query = Booking::with('room') 
                ->where('customer_id', $customerId);

            if (!empty($filters['from']) && !empty($filters['to'])) {
                $query->whereBetween('check_in', [$filters['from'], $filters['to']]);
            }

            if (!empty($filters['status']) && in_array($filters['status'], BookingStatus::values())) {
                $query->where('status', $filters['status']);
            }

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        }
        
        public function getListBookingsByCustomerID($customer_id){
            return Booking::with('customer')->where('customer_id', $customer_id)->get();
        }
    }