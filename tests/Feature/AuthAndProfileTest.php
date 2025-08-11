<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domain\Auth\Entities\User;
use App\Domain\Tenancy\Entities\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

it('issues token on OTP verify and fetches profile', function () {
    // Tenant for Host header
    Tenant::query()->create([
        'name' => 'Tenant A',
        'primary_domain' => 'a.test',
        'domains' => ['a.test'],
        'settings' => [],
        'is_active' => true,
    ]);

    $token = loginWithOtp('a.test');
    expect($token)->toBeString();

    $me = $this->withHeaders(['Authorization' => 'Bearer '.$token, 'Host' => 'a.test'])
        ->getJson('/api/v1/me');
    $me->assertOk();
    $me->assertJsonPath('user.email', 'user@example.com');

    $profile = $this->withHeaders(['Authorization' => 'Bearer '.$token, 'Host' => 'a.test'])
        ->getJson('/api/v1/me/profile');
    $profile->assertOk();
});

it('returns 401 with invalid token', function () {
    // Tenant for Host header
    Tenant::query()->create([
        'name' => 'Tenant A',
        'primary_domain' => 'a.test',
        'domains' => ['a.test'],
        'settings' => [],
        'is_active' => true,
    ]);

    // Test with invalid token
    $me = $this->withHeaders(['Authorization' => 'Bearer invalid-token', 'Host' => 'a.test'])
        ->getJson('/api/v1/me');
    $me->assertStatus(401);

    // Test with no token
    $meNoAuth = $this->withHeaders(['Host' => 'a.test'])
        ->getJson('/api/v1/me');
    $meNoAuth->assertStatus(401);
});

it('otp verify fails with incorrect code', function () {
    // Tenant for Host header
    Tenant::query()->create([
        'name' => 'Tenant A',
        'primary_domain' => 'a.test',
        'domains' => ['a.test'],
        'settings' => [],
        'is_active' => true,
    ]);

    $identifier = 'user@example.com';
    $code = '123456';
    Cache::put('otp:code:'.$identifier, Hash::make($code), now()->addMinutes(10));

    // Test with incorrect code
    $resp = $this->withHeaders(['Host' => 'a.test'])
        ->postJson('/api/v1/auth/otp/verify', [
            'identifier' => $identifier,
            'code' => 'wrong-code',
        ]);
    $resp->assertStatus(422);

    // Test with expired code
    Cache::forget('otp:code:'.$identifier);
    $resp = $this->withHeaders(['Host' => 'a.test'])
        ->postJson('/api/v1/auth/otp/verify', [
            'identifier' => $identifier,
            'code' => $code,
        ]);
    $resp->assertStatus(422);
});



