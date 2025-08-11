<?php

namespace App\Jobs;

use App\Domain\Booking\Entities\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBookingConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Booking $booking
    ) {}

    public function handle(): void
    {
        // TODO: Send actual notification (email/SMS)
        Log::info('Sending booking confirmation', [
            'booking_id' => $this->booking->id,
            'user_email' => $this->booking->user?->email,
        ]);

        // Simulate notification sending
        sleep(1);
    }
}

