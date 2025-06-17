<?php
namespace App\Repositories\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Repositories\Booking\BookingRepositoryInterface;

    class EloquentBookingRepository implements BookingRepositoryInterface{
        public function findById($id)
        {
            return Booking::find($id); 
        }

        public function getAll()
        {
            return Booking::all();
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
        
        public function create($data)
        {
            return Booking::create($data);
        }

        public function update($id, $data)
        {
            $booking = $this->findById($id);
            $booking->update($data);
            return $booking;
        }

        public function delete($id)
        {
            $booking = $this->findById($id);
            return $booking->delete();
        }

        public function getListBookingsByCustomerID($customer_id){
            return Booking::with('customer')->where('customer_id', $customer_id)->get();
        }
    }