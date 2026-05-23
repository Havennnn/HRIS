<?php

namespace App\Http\Resources\Admin\Holiday;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HolidayEditResource extends JsonResource
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
            'date' => $this->date?->toDateString(),
            'date_formatted' => $this->date?->format('M d, Y'),
            'type_value' => $this->type?->value,
            'type' => $this->type?->label(),
            'type_badge' => $this->type?->badge(),
            'is_paid' => $this->is_paid,
            'description' => $this->description,
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
