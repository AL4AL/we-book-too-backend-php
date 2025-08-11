<?php

namespace App\Domain\Profile\Entities;

use App\Domain\Auth\Entities\User;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'user_id', 'data', 'completed_fields', 'completion_score'
    ];

    protected $casts = [
        'data' => 'array',
        'completed_fields' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updateData(array $data): void
    {
        $this->data = array_merge($this->data ?? [], $data);
        $this->recalculateCompletion();
        $this->save();
    }

    public function recalculateCompletion(): void
    {
        // Logic to calculate completion based on schema
        $completed = collect($this->data ?? [])->filter()->count();
        $this->completion_score = min(100, $completed * 10); // Simple calculation
        $this->completed_fields = array_keys($this->data ?? []);
    }
}
