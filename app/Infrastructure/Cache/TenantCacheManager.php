<?php

namespace App\Infrastructure\Cache;

use App\Support\Tenant\TenantContext;
use Illuminate\Support\Facades\Cache;

class TenantCacheManager
{
    private TenantContext $tenantContext;

    public function __construct(TenantContext $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    public function remember(string $key, int $ttl, callable $callback)
    {
        $tenantKey = $this->getTenantKey($key);
        return Cache::remember($tenantKey, $ttl, $callback);
    }

    public function put(string $key, $value, int $ttl): void
    {
        $tenantKey = $this->getTenantKey($key);
        Cache::put($tenantKey, $value, $ttl);
        $this->tagCache($key);
    }

    public function forget(string $key): void
    {
        $tenantKey = $this->getTenantKey($key);
        Cache::forget($tenantKey);
    }

    public function flush(string $tag = null): void
    {
        if ($tag) {
            Cache::tags($this->getTenantTag($tag))->flush();
        } else {
            Cache::tags($this->getTenantTag('all'))->flush();
        }
    }

    private function getTenantKey(string $key): string
    {
        $tenantId = $this->tenantContext->tenantId() ?? 'global';
        return "tenant:{$tenantId}:{$key}";
    }

    private function getTenantTag(string $tag): string
    {
        $tenantId = $this->tenantContext->tenantId() ?? 'global';
        return "tenant:{$tenantId}:{$tag}";
    }

    private function tagCache(string $key): void
    {
        // Add to tenant-specific tag for cache invalidation
        $tenantTag = $this->getTenantTag('all');
        Cache::tags([$tenantTag])->put($this->getTenantKey($key . ':tagged'), true, 3600);
    }
}

