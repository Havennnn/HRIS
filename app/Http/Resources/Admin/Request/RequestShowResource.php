<?php

namespace App\Http\Resources\Admin\Request;

use App\Enums\Status\RequestStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Request
 */
class RequestShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->employee?->full_name,
            'position' => $this->employee?->position?->name,
            'position_level' => $this->employee?->position?->level,
            'department' => $this->employee?->position?->department?->name,
            'type' => $this->type?->label(),
            'type_value' => $this->type?->value,
            'status_value' => $this->status?->value,
            'status' => $this->status?->badge(),
            'message' => $this->message,
            'requested_date' => $this->requested_date?->format('M d, Y'),
            'end_date' => $this->end_date?->format('M d, Y'),
            'days' => $this->days,
            'overtime_hours' => $this->overtime_hours,
        ];
    }
}
