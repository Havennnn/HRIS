<?php

namespace App\Http\Resources\Admin\Holiday;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HolidayIndexResource extends JsonResource
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
            'name' => $this->name,
            'date' => $this->date?->format('M d, Y'),
            'date_raw' => $this->date?->toDateString(),
            'type' => $this->type?->label(),
            'type_value' => $this->type,
            'type_badge' => $this->type?->badge(),
            'description' => $this->description,
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
