<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domain\Auth\Entities\User;
use App\Domain\Tenancy\Entities\Tenant;
use App\Domain\Booking\Entities\Booking;
use App\Domain\Payment\Entities\PaymentGateway;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

it('creates booking and lists tenant-enabled payment gateways', function () {
    $tenant = Tenant::query()->create([
        'name' => 'Tenant Pay',
        'primary_domain' => 'pay.test',
        'domains' => ['pay.test'],
        'settings' => [],
        'is_active' => true,
    ]);

    $stripe = PaymentGateway::query()->create([
        'name' => 'Stripe',
        'code' => 'stripe',
        'is_active' => true,
        'config' => [],
    ]);

    $zarin = PaymentGateway::query()->create([
        'name' => 'Zarinpal',
        'code' => 'zarinpal',
        'is_active' => true,
        'config' => [],
    ]);

    // Attach only Stripe to tenant
    $tenant->paymentGateways()->attach($stripe->id, ['enabled' => true, 'settings' => []]);
    $tenant->paymentGateways()->attach($zarin->id, ['enabled' => false, 'settings' => []]);

    $token = loginWithOtp('pay.test', 'payuser@example.com', '654321');

    // Create a booking
    $create = $this->withHeaders(['Authorization' => 'Bearer '.$token, 'Host' => 'pay.test'])
        ->postJson('/api/v1/bookings', []);
    $create->assertOk();
    $bookingId = $create->json('data.id');

    // List gateways should include only Stripe
    $gateways = $this->withHeaders(['Authorization' => 'Bearer '.$token, 'Host' => 'pay.test'])
        ->getJson('/api/v1/payments/gateways');
    $gateways->assertOk();
    $codes = collect($gateways->json('data'))->pluck('code')->all();
    expect($codes)->toContain('stripe')->not->toContain('zarinpal');

    // Create payment intent via enabled gateway
    $intent = $this->withHeaders(['Authorization' => 'Bearer '.$token, 'Host' => 'pay.test'])
        ->postJson('/api/v1/payments/intents', [
            'booking_id' => $bookingId,
            'gateway_code' => 'stripe',
        ]);
    $intent->assertOk();

    // Attempt with disabled gateway should 404
    $intentBad = $this->withHeaders(['Authorization' => 'Bearer '.$token, 'Host' => 'pay.test'])
        ->postJson('/api/v1/payments/intents', [
            'booking_id' => $bookingId,
            'gateway_code' => 'zarinpal',
        ]);
    $intentBad->assertNotFound();
});



