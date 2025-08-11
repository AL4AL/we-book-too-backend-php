<?php

namespace App\Domain\Booking\Entities;

use App\Models\Concerns\BelongsToTenant;
use App\Domain\Auth\Entities\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'user_id', 'status', 'total_amount', 'currency', 
        'requires_prepayment', 'scheduled_at', 'notes', 'meta'
    ];

    protected $casts = [
        'meta' => 'array',
        'requires_prepayment' => 'boolean',
        'scheduled_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, ['in_progress']);
    }

    public function markAsSucceeded(): void
    {
        $this->update(['status' => 'succeeded']);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function markAsCanceled(): void
    {
        $this->update(['status' => 'canceled']);
    }
}
