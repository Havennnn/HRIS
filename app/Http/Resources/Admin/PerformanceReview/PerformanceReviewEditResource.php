<?php

namespace App\Http\Resources\Admin\PerformanceReview;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceReviewEditResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'reviewer_id' => $this->reviewer_id,
            'review_date' => $this->review_date?->toDateString(),
            'overall_score' => $this->overall_score,
            'status' => $this->status?->value,
            'scores' => $this->reviewScores->map(fn ($score) => [
                'id' => $score->id,
                'kpi_id' => $score->kpi_id,
                'score' => $score->score,
            ]),
            'feedback' => $this->reviewFeedback->map(fn ($item) => [
                'id' => $item->id,
                'type' => $item->type?->value,
                'feedback' => $item->feedback,
            ]),
        ];
    }
}
