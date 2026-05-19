<?php

namespace App\Http\Controllers\Api\V1\PerformanceReview;

use App\Http\Controllers\Controller;
use App\Models\PerformanceReview;
use App\Services\Api\V1\PerformanceReview\PerformanceReviewApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PerformanceReviewController extends Controller
{
    public function __construct(
        protected PerformanceReviewApiService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        return $this->service->list($request);
    }

    public function show(PerformanceReview $performanceReview, Request $request): JsonResponse
    {
        return $this->service->show($performanceReview, $request);
    }
}
