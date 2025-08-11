<?php

namespace App\Application\Booking\UseCases;

use App\Application\Booking\Contracts\BookingRepositoryInterface;
use App\Application\Booking\DTOs\CreateBookingDTO;
use App\Domain\Booking\Entities\Booking;
use App\Domain\Booking\Events\BookingCreated;

class CreateBookingUseCase
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepository
    ) {}

    public function execute(CreateBookingDTO $dto): Booking
    {
        $booking = $this->bookingRepository->create([
            'user_id' => $dto->userId,
            'scheduled_at' => $dto->scheduledAt,
            'notes' => $dto->notes,
            'status' => 'in_progress',
        ]);

        foreach ($dto->items as $itemData) {
            $this->bookingRepository->addItem($booking, $itemData);
        }

        // Emit domain event
        event(new BookingCreated($booking));

        return $booking->fresh(['items', 'user']);
    }
}

