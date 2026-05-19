<?php

namespace App\Http\Resources\Api\V1\PerformanceReview;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reviewer' => $this->reviewer ? [
                'id' => $this->reviewer->id,
                'name' => $this->reviewer->full_name,
            ] : null,
            'review_date' => $this->review_date?->toDateString(),
            'overall_score' => $this->overall_score,
            'status' => $this->status?->badge(),
            'scores' => $this->reviewScores->map(fn ($score) => [
                'kpi' => $score->kpi?->name,
                'score' => $score->score,
            ]),
            'feedback' => $this->reviewFeedback->map(fn ($item) => [
                'type' => $item->type?->label(),
                'feedback' => $item->feedback,
            ]),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
