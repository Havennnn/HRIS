<?php

namespace App\Http\Resources\Admin\Employee\Attendance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceIndexResource extends JsonResource
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
            'date' => $this->date?->format('M d, Y'),
            'time_in' => $this->time_in?->format('h:i A'),
            'time_out' => $this->time_out?->format('h:i A'),
            'late_minutes' => $this->late_minutes,
            'overtime_minutes' => $this->overtime_minutes,
            'status' => $this->status?->badge(),
            'status_value' => $this->status?->value,
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
