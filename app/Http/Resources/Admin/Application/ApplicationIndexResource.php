<?php

namespace App\Http\Resources\Admin\Application;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationIndexResource extends JsonResource
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
            'position' => $this->career?->position?->name . ' - ' . $this->career?->position?->level,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'status' => $this->status?->badge(),
            'birthdate' => $this->birthdate?->format('M d, Y'),
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
