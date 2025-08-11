<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domain\Tenancy\Entities\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

it('exposes metrics endpoint and chat request flow works', function () {
    Tenant::query()->create([
        'name' => 'Tenant C',
        'primary_domain' => 'c.test',
        'domains' => ['c.test'],
        'settings' => [],
        'is_active' => true,
    ]);

    $token = loginWithOtp('c.test', 'chatuser@example.com', '111222');

    // Metrics (no auth required by current routes; Host header still needed for middleware)
    $metrics = $this->withHeaders(['Host' => 'c.test'])->get('/api/v1/metrics');
    $metrics->assertOk();
    expect($metrics->headers->get('Content-Type'))->toContain('text/plain');

    // Chat request
    $req = $this->withHeaders(['Authorization' => 'Bearer '.$token, 'Host' => 'c.test'])
        ->postJson('/api/v1/chat/requests', []);
    $req->assertOk();
    $conversationId = $req->json('data.id');

    // List conversations should include the new one
    $list = $this->withHeaders(['Authorization' => 'Bearer '.$token, 'Host' => 'c.test'])
        ->getJson('/api/v1/chat/conversations');
    $list->assertOk();
    $ids = collect($list->json('data'))->pluck('id');
    expect($ids)->toContain($conversationId);
});



