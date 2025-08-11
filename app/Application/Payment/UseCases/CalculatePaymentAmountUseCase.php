<?php

namespace App\Application\Payment\UseCases;

use App\Application\Payment\DTOs\PaymentCalculationDTO;
use App\Domain\Booking\Entities\Booking;
use App\Domain\Payment\Entities\PaymentGateway;

class CalculatePaymentAmountUseCase
{
    public function execute(Booking $booking, PaymentGateway $gateway): PaymentCalculationDTO
    {
        $subtotal = $booking->total_amount;
        
        // Gateway fee calculation (example: 2.9% + $0.30)
        $gatewayFee = ($subtotal * 0.029) + 0.30;
        
        // Tax calculation (example: 10%)
        $tax = $subtotal * 0.1;
        
        // Total
        $total = $subtotal + $gatewayFee + $tax;

        return new PaymentCalculationDTO(
            subtotal: $subtotal,
            gatewayFee: round($gatewayFee, 2),
            tax: round($tax, 2),
            total: round($total, 2),
            currency: $booking->currency
        );
    }
}

