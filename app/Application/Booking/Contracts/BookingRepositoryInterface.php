<?php

namespace App\Application\Booking\Contracts;

use App\Domain\Booking\Entities\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookingRepositoryInterface
{
    public function findById(int $id): ?Booking;
    public function findByUserId(int $userId, ?string $status = null): LengthAwarePaginator;
    public function create(array $data): Booking;
    public function update(Booking $booking, array $data): Booking;
    public function addItem(Booking $booking, array $itemData): void;
    public function calculateTotal(Booking $booking): void;
}

