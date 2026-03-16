<?php

namespace App\Http\Resources\Admin\Career;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position' => [
                'id' => $this->position?->id,
                'name' => $this->position?->name,
            ],
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
