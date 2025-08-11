<?php

namespace App\Http\Controllers;

use App\Domain\Payment\Entities\PaymentGateway;
use App\Domain\Booking\Entities\Booking;
use App\Domain\Payment\Entities\Payment;
use Illuminate\Http\Request;
use App\Support\Tenant\TenantContext;

class PaymentController extends Controller
{
    public function gateways(Request $request)
    {
        /** @var TenantContext $tenantContext */
        $tenantContext = app(TenantContext::class);
        $tenant = $tenantContext->tenant;
        if (!$tenant) {
            abort(404);
        }

        $gateways = $tenant
            ->paymentGateways()
            ->where('payment_gateways.is_active', true)
            ->wherePivot('enabled', true)
            ->get(['payment_gateways.id', 'payment_gateways.name', 'payment_gateways.code']);

        return response()->json(['data' => $gateways]);
    }

    public function createIntent(Request $request)
    {
        $data = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'gateway_code' => 'required|string',
        ]);

        $booking = Booking::findOrFail($data['booking_id']);
        $this->authorize('pay', $booking);

        if (!$booking->canBePaid()) {
            return response()->json(['error' => 'Booking cannot be paid'], 422);
        }

        /** @var TenantContext $tenantContext */
        $tenantContext = app(TenantContext::class);
        $tenant = $tenantContext->tenant;
        if (!$tenant) {
            abort(404);
        }

        $gateway = $tenant
            ->paymentGateways()
            ->where('payment_gateways.is_active', true)
            ->wherePivot('enabled', true)
            ->where('payment_gateways.code', $data['gateway_code'])
            ->firstOrFail();

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'gateway_id' => $gateway->id,
            'amount' => $booking->total_amount,
            'currency' => $booking->currency,
            'status' => 'pending',
        ]);

        // TODO: Implement gateway-specific payment intent creation
        return response()->json([
            'payment_id' => $payment->id,
            'redirect_url' => 'https://gateway.example.com/pay/' . $payment->id,
            'client_secret' => 'mock_client_secret',
        ]);
    }

    public function calculate(Request $request)
    {
        $data = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'gateway_code' => 'required|string',
        ]);

        $booking = Booking::with('items')->findOrFail($data['booking_id']);
        /** @var TenantContext $tenantContext */
        $tenantContext = app(TenantContext::class);
        $tenant = $tenantContext->tenant;
        if (!$tenant) {
            abort(404);
        }

        $gateway = $tenant
            ->paymentGateways()
            ->where('payment_gateways.is_active', true)
            ->wherePivot('enabled', true)
            ->where('payment_gateways.code', $data['gateway_code'])
            ->firstOrFail();

        $subtotal = $booking->total_amount;
        $gatewayFee = $subtotal * 0.029; // 2.9% example fee
        $tax = $subtotal * 0.1; // 10% tax example
        $total = $subtotal + $gatewayFee + $tax;

        return response()->json([
            'breakdown' => [
                'subtotal' => $subtotal,
                'gateway_fee' => round($gatewayFee, 2),
                'tax' => round($tax, 2),
                'total' => round($total, 2),
            ],
            'currency' => $booking->currency,
        ]);
    }
}
