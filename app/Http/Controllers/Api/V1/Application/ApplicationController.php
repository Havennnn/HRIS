<?php

namespace App\Http\Controllers\Api\V1\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Application\ApplicationSubmitRequest;
use App\Services\Api\V1\Application\ApplicationApiService;
use Illuminate\Http\JsonResponse;

class ApplicationController extends Controller
{
    public function __construct(
        protected ApplicationApiService $applicationService
    ) {}

    public function store(ApplicationSubmitRequest $request): JsonResponse
    {
        return $this->applicationService->submit($request);
    }
}
