<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Enums\RoleSystem;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->role_name, [RoleSystem::MANAGER, RoleSystem::STAFF]);
    }

    private function isOwner(User $user, Booking $booking): bool
    {
        return $booking->customer->id === $user->customer->id;
    }

    public function view(User $user, Booking $booking): bool
    {
        return in_array($user->role->role_name, [RoleSystem::MANAGER, RoleSystem::STAFF])
            || $this->isOwner($user, $booking);
    }

    public function payRoom(User $user, Booking $booking): bool {
        return $this->isOwner($user, $booking)
            && $booking->status === BookingStatus::PENDING_PAYMENT;
    }

    public function bookAndPayAdditionalBooking(User $user, Booking $booking): bool {
        return $this->isOwner($user, $booking)
            && $booking->status === BookingStatus::CONFIRMED;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }
}