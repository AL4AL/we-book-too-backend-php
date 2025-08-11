<?php

namespace App\Listeners;

use App\Domain\Booking\Events\BookingCreated;
use App\Jobs\SendBookingConfirmationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;

class BookingCreatedListener implements ShouldQueue
{
    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking;

        // Log the event
        Log::info('Booking created', [
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'total_amount' => $booking->total_amount,
        ]);

        // Update metrics
        $registry = new CollectorRegistry(new InMemory());
        $counter = $registry->getOrRegisterCounter('app', 'booking_created_total', 'Total bookings created', ['tenant']);
        $counter->inc([app(\App\Support\Tenant\TenantContext::class)->tenantId() ?? 'unknown']);

        // Dispatch notification job
        SendBookingConfirmationJob::dispatch($booking);
    }
}

