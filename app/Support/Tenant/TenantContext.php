<?php

namespace App\Support\Tenant;

use App\Domain\Tenancy\Entities\Tenant;

class TenantContext
{
    public function __construct(
        public readonly ?Tenant $tenant = null,
    ) {
    }

    public function tenantId(): ?int
    {
        return $this->tenant?->id;
    }
}


