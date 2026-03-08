<?php

namespace App\Http\Resources\Admin\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeIndexResource extends JsonResource
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
            'position' => $this->position?->name,
            'department' => $this->position?->department?->name,
            'full_name' => $this->full_name,
            'birthdate' => $this->birthdate?->toDateString(),
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
            'status' => $this->status?->badge(),
            'type' => $this->type?->label(),
            'hired_date' => $this->hired_date?->format('M d, Y'),
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
