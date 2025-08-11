<?php

namespace App\Infrastructure\Persistence;

use App\Application\Booking\Contracts\BookingRepositoryInterface;
use App\Domain\Booking\Entities\Booking;
use App\Domain\Booking\Entities\BookingItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentBookingRepository implements BookingRepositoryInterface
{
    public function findById(int $id): ?Booking
    {
        return Booking::with(['items', 'user'])->find($id);
    }

    public function findByUserId(int $userId, ?string $status = null): LengthAwarePaginator
    {
        $query = Booking::query()->with(['items', 'user'])->where('user_id', $userId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function update(Booking $booking, array $data): Booking
    {
        $booking->update($data);
        return $booking->fresh();
    }

    public function addItem(Booking $booking, array $itemData): void
    {
        $item = new BookingItem($itemData);
        $item->calculateSubtotal();
        $booking->items()->save($item);
        $this->calculateTotal($booking);
    }

    public function calculateTotal(Booking $booking): void
    {
        $total = $booking->items->sum('subtotal');
        $booking->update(['total_amount' => $total]);
    }
}

