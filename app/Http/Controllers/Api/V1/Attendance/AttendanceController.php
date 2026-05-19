<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Http\Controllers\Controller;
use App\Services\Api\V1\Attendance\AttendanceApiService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceApiService $service
    ) {}

    public function list(Request $request): Responsable
    {
        return $this->service->list($request);
    }

    public function today(Request $request): JsonResponse
    {
        return $this->service->today($request);
    }

    public function timeIn(Request $request): JsonResponse
    {
        return $this->service->timeIn($request);
    }

    public function timeOut(Request $request): JsonResponse
    {
        return $this->service->timeOut($request);
    }
}
