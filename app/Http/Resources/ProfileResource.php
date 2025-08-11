<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Profile\Entities\Profile */
class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data' => $this->data,
            'completed_fields' => $this->completed_fields,
            'completion_score' => $this->completion_score,
            'updated_at' => $this->updated_at,
        ];
    }
}
