<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $identifier,
        public string $code,
        public string $channel
    ) {}

    public function handle(): void
    {
        // TODO: Integrate with actual SMS/Email providers
        Log::info('Sending OTP', [
            'identifier' => $this->identifier,
            'channel' => $this->channel,
            'code' => $this->code, // Remove in production
        ]);

        // Simulate sending
        sleep(1);
    }
}

