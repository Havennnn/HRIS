<?php

namespace App\Http\Resources\Api\V1\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'first_name'       => $this->first_name,
            'middle_name'      => $this->middle_name,
            'last_name'        => $this->last_name,
            'full_name'        => $this->full_name,
            'email'            => $this->email,
            'mobile_number'    => $this->mobile_number,
            'birthdate'        => $this->birthdate?->toDateString(),
            'hired_date'       => $this->hired_date?->toDateString(),
            'status'           => $this->status?->badge(),
            'type'             => $this->type?->label(),
            'position'         => [
                'id'         => $this->position?->id,
                'name'       => $this->position?->name,
                'level'      => $this->position?->level,
                'department' => $this->position?->department?->name,
            ],
            'contact_person' => $this->contact ? [
                'name' => $this->contact->name,
                'type' => $this->contact->type?->label(),
                'mobile_number' => $this->contact->mobile_number,
            ] : null,
            'device' => $this->device ? [
                'desktop' => $this->device->desktop,
                'laptop' => $this->device->laptop,
            ] : null,
        ];
    }
}
