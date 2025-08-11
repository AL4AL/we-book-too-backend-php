<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;

class MetricsRequestTiming
{
    private static ?CollectorRegistry $registry = null;

    private static function registry(): CollectorRegistry
    {
        if (!self::$registry) {
            self::$registry = new CollectorRegistry(new InMemory());
        }
        return self::$registry;
    }

    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $start;

        $registry = self::registry();
        $histogram = $registry->getOrRegisterHistogram('app', 'http_request_duration_seconds', 'Request duration', ['route', 'method']);
        $routeName = optional($request->route())->uri() ?? 'unknown';
        $histogram->observe($duration, [$routeName, $request->getMethod()]);

        return $response;
    }
}


