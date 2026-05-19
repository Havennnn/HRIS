<?php

namespace App\Http\Resources\Api\V1\Request;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type?->badge(),
            'status' => $this->status?->badge(),
            'message' => $this->message,
            'requested_date' => $this->requested_date?->toDateString(),
            'days' => $this->days,
            'end_date' => $this->end_date?->toDateString(),
            'overtime_hours' => $this->overtime_hours,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
