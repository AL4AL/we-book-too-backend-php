<?php

namespace App\Http\Controllers;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;

class MetricsController extends Controller
{
    public function __invoke()
    {
        $registry = new CollectorRegistry(new InMemory());
        $renderer = new RenderTextFormat();
        $metrics = $renderer->render($registry->getMetricFamilySamples());
        return response($metrics, 200, ['Content-Type' => RenderTextFormat::MIME_TYPE]);
    }
}


