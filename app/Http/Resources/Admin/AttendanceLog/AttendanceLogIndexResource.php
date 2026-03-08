<?php

namespace App\Http\Resources\Admin\AttendanceLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceLogIndexResource extends JsonResource
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
            'employee' => $this->employee?->full_name,
            'employee_id' => $this->employee_id,
            'type' => $this->type?->label(),
            'type_value' => $this->type?->value,
            'timestamp' => $this->timestamp?->format('M d, Y h:i A'),
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
