<?php

namespace App\Logging;

use App\Support\Tenant\TenantContext;
use Monolog\Logger;

class AddTenantContext
{
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getProcessors() as $processor) {
            // Keep existing processors
        }

        $logger->pushProcessor(function (array $record) {
            $tenantId = app(TenantContext::class)->tenantId();
            $record['extra']['tenant_id'] = $tenantId;
            return $record;
        });
    }
}


