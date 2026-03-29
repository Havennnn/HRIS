<?php

namespace App\Http\Resources\Admin\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $contactPerson = $this->contact;
        $employeeDocument = $this->documents->first();

        return [
            'id' => $this->id,
            'position_id' => $this->position?->id,
            'position' => $this->position?->name,
            'position_level' => $this->position?->level,
            'department_id' => $this->position?->department?->id,
            'department' => $this->position?->department?->name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'birthdate' => $this->birthdate?->toDateString(),
            'birthdate_formatted' => $this->birthdate?->format('M d, Y'),
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
            'status_value' => $this->status?->value,
            'status' => $this->status?->badge(),
            'type_value' => $this->type,
            'type' => $this->type?->label(),
            'hired_date' => $this->hired_date?->format('M d, Y'),
            'created_at' => $this->created_at?->format('M d, Y'),
            'contact_person' => $contactPerson ? [
                'name' => $contactPerson->name,
                'type_value' => $contactPerson->type?->value,
                'type' => $contactPerson->type?->label(),
                'mobile_number' => $contactPerson->mobile_number,
            ] : null,
            'documents' => [
                'sss' => $employeeDocument?->sssFile?->preview(),
                'philhealth' => $employeeDocument?->philhealthFile?->preview(),
                'bir' => $employeeDocument?->birFile?->preview(),
                'medical' => $employeeDocument?->medicalFile?->preview(),
            ],
            'device' => $this->device ? [
                'desktop' => $this->device?->desktop ?? '',
                'laptop' => $this->device?->laptop ?? '',
            ] : null,
        ];
    }
}
