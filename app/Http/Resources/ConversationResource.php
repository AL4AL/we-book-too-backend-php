<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Chat\Entities\Conversation */
class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'service' => new ServiceResource($this->whenLoaded('service')),
            'specialist' => new SpecialistResource($this->whenLoaded('specialist')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
