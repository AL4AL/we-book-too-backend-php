<?php

namespace App\Application\Payment\DTOs;

class PaymentCalculationDTO
{
    public function __construct(
        public readonly float $subtotal,
        public readonly float $gatewayFee,
        public readonly float $tax,
        public readonly float $total,
        public readonly string $currency
    ) {}

    public function toArray(): array
    {
        return [
            'breakdown' => [
                'subtotal' => $this->subtotal,
                'gateway_fee' => $this->gatewayFee,
                'tax' => $this->tax,
                'total' => $this->total,
            ],
            'currency' => $this->currency,
        ];
    }
}

