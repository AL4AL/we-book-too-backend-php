<?php

namespace App\Http\Controllers;

use App\Domain\Booking\Entities\Booking;
use App\Domain\Booking\Entities\BookingItem;
use App\Http\Resources\BookingResource;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query()->with(['items', 'user'])->where('user_id', auth()->id());
        
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        
        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);
        return BookingResource::collection($bookings);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'scheduled_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'status' => 'in_progress',
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return new BookingResource($booking->load(['items', 'user']));
    }

    public function show(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);
        return new BookingResource($booking->load(['items', 'user']));
    }

    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        $data = $request->validate([
            'scheduled_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booking->update($data);
        return new BookingResource($booking->load(['items', 'user']));
    }
}
