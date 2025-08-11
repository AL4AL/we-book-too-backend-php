<?php

namespace App\Domain\Booking\Entities;

use App\Domain\Catalog\Entities\Service;
use App\Domain\Catalog\Entities\Specialist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'service_id', 'specialist_id', 'unit_price', 'qty', 'subtotal', 'meta'
    ];

    protected $casts = [
        'meta' => 'array',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }

    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->unit_price * $this->qty;
    }
}
