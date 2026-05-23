<?php

namespace App\Http\Resources\Admin\Career;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerEditResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position_id' => $this->position_id,
            'position' => $this->position?->name,
            'description' => $this->description,
            'salary' => $this->salary,
            'status_value' => $this->status?->value,
            'status' => $this->status?->badge(),
            'meta_title' => $this->seoMeta?->meta_title,
            'meta_description' => $this->seoMeta?->meta_description,
            'og_title' => $this->seoMeta?->og_title,
            'og_description' => $this->seoMeta?->og_description,
            'og_image' => $this->seoMeta?->og_image,
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }
}
