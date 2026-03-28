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
        // Determine device status
        $deviceStatus = 'No Device';
        if ($this->device) {
            $desktop = $this->device->desktop ?? '';
            $laptop = $this->device->laptop ?? '';

            if ($desktop !== '' && $laptop !== '') {
                $deviceStatus = 'completed';
            } elseif ($desktop !== '' && $laptop === '') {
                $deviceStatus = 'only desktop';
            } elseif ($desktop === '' && $laptop !== '') {
                $deviceStatus = 'only laptop';
            }
        }

        return [
            'id' => $this->id,
            'position' => $this->position?->name,
            'department' => $this->position?->department?->name,
            'full_name' => $this->full_name,
            'birthdate' => $this->birthdate?->format('M d, Y'),
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
            'status' => $this->status?->badge(),
            'type' => $this->type?->label(),
            'device' => $deviceStatus,
            'hired_date' => $this->hired_date?->format('M d, Y'),
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
