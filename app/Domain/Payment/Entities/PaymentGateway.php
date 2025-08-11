<?php

namespace App\Domain\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'is_active', 'config'
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(\App\Domain\Tenancy\Entities\Tenant::class, 'payment_gateway_tenant', 'gateway_id', 'tenant_id')
            ->withPivot(['enabled', 'settings'])
            ->withTimestamps();
    }
}
