<?php

namespace App\Http\Middleware;

use App\Domain\Tenancy\Entities\Tenant;
use App\Support\Tenant\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $this->extractHostFromRequest($request);
        if (!$host) {
            return response('Referer or Host header required', 400);
        }

        $tenant = $this->findTenantByHost($host);
        if (!$tenant || !$tenant->is_active) {
            abort(404);
        }

        app()->instance(TenantContext::class, new TenantContext($tenant));

        return $next($request);
    }

    private function extractHostFromRequest(Request $request): ?string
    {
        $referer = $request->headers->get('referer') ?? $request->headers->get('origin');
        if ($referer) {
            $host = parse_url($referer, PHP_URL_HOST);
            if ($host) {
                return strtolower($host);
            }
        }

        $host = $request->getHost();
        return $host ? strtolower($host) : null;
    }

    private function findTenantByHost(string $host): ?Tenant
    {
        $cacheKey = "tenant_by_host:".$host;
        $minutes = (int) config('tenancy.cache_minutes', 10);
        return Cache::remember($cacheKey, now()->addMinutes($minutes), function () use ($host) {
            return Tenant::query()
                ->where('primary_domain', $host)
                ->orWhereJsonContains('domains', $host)
                ->first();
        });
    }
}


