<?php

namespace App\Http\Resources\Admin\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'full_name' => $this->full_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'birthdate' => $this->birthdate?->format('M d, Y'),
            'position' => $this->position?->name,
            'department' => $this->position?->department?->name,
            'status' => $this->status?->label(),
            'type' => $this->type?->label(),
            'hired_date' => $this->hired_date?->format('M d, Y'),
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
