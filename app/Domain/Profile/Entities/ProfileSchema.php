<?php

namespace App\Domain\Profile\Entities;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileSchema extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'profile_schema';

    protected $fillable = [
        'tenant_id', 'fields'
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    public function getRequiredFields(): array
    {
        return collect($this->fields ?? [])
            ->where('required', true)
            ->keys()
            ->toArray();
    }

    public function getOptionalFields(): array
    {
        return collect($this->fields ?? [])
            ->where('required', false)
            ->keys()
            ->toArray();
    }
}
