<?php
namespace App\Repositories\Booking;
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
    }