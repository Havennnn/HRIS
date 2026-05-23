<?php

namespace App\Http\Resources\Admin\Career;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerIndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position' => [
                'id' => $this->position?->id,
                'name' => $this->position?->name,
            ],
            'description' => $this->description,
            'salary' => $this->salary,
            'status' => $this->status?->badge(),
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
