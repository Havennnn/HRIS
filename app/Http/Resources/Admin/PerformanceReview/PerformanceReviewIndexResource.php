<?php

namespace App\Http\Resources\Admin\PerformanceReview;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceReviewIndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee' => $this->employee ? [
                'id' => $this->employee->id,
                'name' => $this->employee->full_name,
            ] : null,
            'reviewer' => $this->reviewer ? [
                'id' => $this->reviewer->id,
                'name' => $this->reviewer->full_name,
            ] : null,
            'review_date' => $this->review_date?->toDateString(),
            'overall_score' => $this->overall_score,
            'status' => $this->status?->badge(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
