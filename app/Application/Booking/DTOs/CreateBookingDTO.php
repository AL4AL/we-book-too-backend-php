<?php

namespace App\Application\Booking\DTOs;

class CreateBookingDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly ?string $scheduledAt,
        public readonly ?string $notes,
        public readonly array $items = []
    ) {}

    public static function fromRequest(array $data, int $userId): self
    {
        return new self(
            userId: $userId,
            scheduledAt: $data['scheduled_at'] ?? null,
            notes: $data['notes'] ?? null,
            items: $data['items'] ?? []
        );
    }
}

