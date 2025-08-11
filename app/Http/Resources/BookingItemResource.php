<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Booking\Entities\BookingItem */
class BookingItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'specialist_id' => $this->specialist_id,
            'unit_price' => $this->unit_price,
            'qty' => $this->qty,
            'subtotal' => $this->subtotal,
            'service' => new ServiceResource($this->whenLoaded('service')),
            'specialist' => new SpecialistResource($this->whenLoaded('specialist')),
        ];
    }
}
