<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Chat\Entities\Message */
class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'content' => $this->content,
            'sender' => $this->when(!$this->isSystemMessage(), [
                'id' => $this->sender?->id,
                'name' => $this->sender?->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
