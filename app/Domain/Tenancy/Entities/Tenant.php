<?php

namespace App\Domain\Tenancy\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'primary_domain',
        'domains',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'domains' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function paymentGateways(): BelongsToMany
    {
        return $this->belongsToMany(\App\Domain\Payment\Entities\PaymentGateway::class, 'payment_gateway_tenant', 'tenant_id', 'gateway_id')
            ->withPivot(['enabled', 'settings'])
            ->withTimestamps();
    }
}


