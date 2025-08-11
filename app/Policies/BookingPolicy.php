<?php

namespace App\Policies;

use App\Domain\Auth\Entities\User;
use App\Domain\Booking\Entities\Booking;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id && $booking->status === 'in_progress';
    }

    public function pay(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id && $booking->canBePaid();
    }
}
