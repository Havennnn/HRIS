<?php

namespace App\Http\Resources\Admin\Application;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationShowResource extends JsonResource
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
            'position' => $this->career?->position?->name.' - '.$this->career?->position?->level,
            'full_name' => $this->full_name,
            'birthdate' => $this->birthdate?->format('M d, Y'),
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
            'status' => $this->status?->badge(),
            'status_value' => $this->status?->value,
            'details' => $this->details ? [
                'id' => $this->details->id,
                // Add other application details fields as needed
            ] : null,
            'interview' => $this->interview ? [
                'id' => $this->interview->id,
                // Add other interview fields as needed
            ] : null,
            'created_at' => $this->created_at?->format('M d, Y'),
            'updated_at' => $this->updated_at?->format('M d, Y'),
        ];
    }
}
