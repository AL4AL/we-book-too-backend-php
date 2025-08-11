<?php

namespace App\Domain\Chat\Entities;

use App\Domain\Auth\Entities\User;
use App\Domain\Catalog\Entities\Service;
use App\Domain\Catalog\Entities\Specialist;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'service_id', 'specialist_id', 'created_by_user_id', 
        'representative_user_id', 'status'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function representativeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'representative_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function accept(int $representativeUserId): void
    {
        $this->update([
            'status' => 'accepted',
            'representative_user_id' => $representativeUserId,
        ]);
    }

    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    public function canBeAccepted(): bool
    {
        return $this->status === 'requested';
    }
}
