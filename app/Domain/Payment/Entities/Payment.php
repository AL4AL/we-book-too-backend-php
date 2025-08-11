<?php

namespace App\Domain\Payment\Entities;

use App\Domain\Booking\Entities\Booking;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'booking_id', 'gateway_id', 'amount', 'currency', 
        'status', 'provider_ref', 'meta'
    ];

    protected $casts = [
        'meta' => 'array',
        'amount' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function markAsAuthorized(string $providerRef = null): void
    {
        $this->update([
            'status' => 'authorized',
            'provider_ref' => $providerRef,
        ]);
    }

    public function markAsCaptured(): void
    {
        $this->update(['status' => 'captured']);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}
