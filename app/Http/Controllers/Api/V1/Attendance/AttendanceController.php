<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Http\Controllers\Controller;
use App\Services\Api\V1\Attendance\AttendanceApiService;
use App\Traits\BuildsApiResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use BuildsApiResponses;

    public function __construct(
        protected AttendanceApiService $attendanceService
    ) { }

    public function list(Request $request): JsonResponse
    {
        try {
            $data = $this->attendanceService->listAttendances($request->user());

            return $this->successResponse($data);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $this->resolveErrorStatusCode($e));
        }
    }

    public function today(Request $request): JsonResponse
    {
        try {
            $data = $this->attendanceService->getTodayAttendance($request->user());

            return $this->successResponse($data);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $this->resolveErrorStatusCode($e));
        }
    }

    public function timeIn(Request $request): JsonResponse
    {
        try {
            $data = $this->attendanceService->timeIn($request->user());

            return $this->successResponse($data, 'Time in recorded successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $this->resolveErrorStatusCode($e));
        }
    }

    public function timeOut(Request $request): JsonResponse
    {
        try {
            $data = $this->attendanceService->timeOut($request->user());

            return $this->successResponse($data, 'Time out recorded successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $this->resolveErrorStatusCode($e));
        }
    }

    private function resolveErrorStatusCode(Exception $exception): int
    {
        $message = $exception->getMessage();

        if (str_contains($message, 'Unauthorized.')) {
            return 401;
        }

        if (str_contains($message, 'already') || str_contains($message, 'No active')) {
            return 422;
        }

        return 500;
    }
}
