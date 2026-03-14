<?php

namespace App\Http\Resources\Admin\Request;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestIndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->employee?->full_name,
            'position' => $this->employee?->position?->name,
            'department' => $this->employee?->position?->department?->name,
            'type' => $this->type?->label(),
            'status_value' => $this->status?->value,
            'status' => $this->status?->badge(),
            'message' => $this->message,
            'requested_date' => $this->requested_date?->format('M d, Y'),
            'end_date' => $this->end_date?->format('M d, Y'),
            'days' => $this->days,
            'overtime_hours' => $this->overtime_hours,
            'created_at' => $this->created_at?->format('M d, Y H:i:s'),
        ];
    }
}
