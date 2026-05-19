<?php

namespace App\Http\Resources\Api\V1\Career;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position' => [
                'id' => $this->position?->id,
                'name' => $this->position?->name,
                'level' => $this->position?->level,
                'department' => $this->position?->department?->name,
            ],
            'description' => $this->description,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
