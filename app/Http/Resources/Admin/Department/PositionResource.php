<?php

namespace App\Http\Resources\Admin\Department;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Position
 */
class PositionResource extends JsonResource
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
            'department_id' => $this->department_id,
            'department' => $this->department?->name,
            'name' => $this->name,
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
