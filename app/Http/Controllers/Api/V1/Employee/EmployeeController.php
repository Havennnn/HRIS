<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Employee\EmployeeDocumentUploadRequest;
use App\Services\Api\V1\Employee\EmployeeApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeApiService $employeeService
    ) {}

    public function show(Request $request): JsonResponse
    {
        return $this->employeeService->getEmployeeProfile($request);
    }

    public function uploadDocuments(EmployeeDocumentUploadRequest $request): JsonResponse
    {
        return $this->employeeService->uploadDocuments($request);
    }

    public function listDocuments(Request $request): JsonResponse
    {
        return $this->employeeService->getDocuments($request);
    }

    public function performanceMetrics(Request $request): JsonResponse
    {
        return $this->employeeService->getPerformanceMetrics($request);
    }
}
