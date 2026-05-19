<?php

namespace App\Http\Controllers\Api\V1\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Request\RequestSubmitRequest;
use App\Models\Request as RequestModel;
use App\Services\Api\V1\Request\RequestApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function __construct(
        protected RequestApiService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        return $this->service->list($request);
    }

    public function show(RequestModel $request): JsonResponse
    {
        return $this->service->show($request);
    }

    public function store(RequestSubmitRequest $request): JsonResponse
    {
        return $this->service->submit($request);
    }
}
