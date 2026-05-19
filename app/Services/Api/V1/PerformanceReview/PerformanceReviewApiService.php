<?php

namespace App\Services\Api\V1\PerformanceReview;

use App\Models\Employee;
use App\Models\PerformanceReview;
use App\Traits\BuildsApiResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PerformanceReviewApiService
{
    use BuildsApiResponses;

    public function list(Request $request): JsonResponse
    {
        $employee = $request->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse();
        }

        try {
            $data = PerformanceReview::query()
                ->with(['reviewer', 'reviewScores.kpi', 'reviewFeedback'])
                ->where('employee_id', $employee->id)
                ->orderByDesc('review_date')
                ->orderByDesc('id')
                ->get()
                ->map(fn (PerformanceReview $review) => $this->formatReview($review))
                ->all();

            return $this->successResponse($data);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve performance reviews: '.$e->getMessage(), 500);
        }
    }

    public function show(PerformanceReview $review, Request $request): JsonResponse
    {
        $employee = $request->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse();
        }

        if ($review->employee_id !== $employee->id) {
            return $this->errorResponse('Performance review not found.', 404);
        }

        try {
            $review->load(['reviewer', 'reviewScores.kpi', 'reviewFeedback']);

            return $this->successResponse($this->formatReview($review));
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve performance review: '.$e->getMessage(), 500);
        }
    }

    private function formatReview(PerformanceReview $review): array
    {
        return [
            'id' => $review->id,
            'reviewer' => $review->reviewer ? [
                'id' => $review->reviewer->id,
                'name' => $review->reviewer->full_name,
            ] : null,
            'review_date' => $review->review_date?->toDateString(),
            'overall_score' => $review->overall_score,
            'status' => $review->status?->badge(),
            'scores' => $review->reviewScores->map(fn ($score) => [
                'kpi' => $score->kpi?->name,
                'score' => $score->score,
            ]),
            'feedback' => $review->reviewFeedback->map(fn ($item) => [
                'type' => $item->type?->label(),
                'feedback' => $item->feedback,
            ]),
            'created_at' => $review->created_at?->toISOString(),
        ];
    }
}
