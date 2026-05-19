<?php

namespace App\Http\Resources\Api\V1\Application;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'career_id' => $this->career_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'full_name' => $this->full_name,
            'birthdate' => $this->birthdate?->toDateString(),
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
            'status' => $this->status?->badge(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
