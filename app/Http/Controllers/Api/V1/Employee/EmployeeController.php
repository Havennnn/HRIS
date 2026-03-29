<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Employee\EmployeeDocumentUploadRequest;
use App\Http\Resources\Api\V1\Employee\EmployeeProfileResource;
use App\Services\Api\V1\Employee\EmployeeApiService;
use App\Traits\BuildsApiResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use BuildsApiResponses;

    public function __construct(
        protected EmployeeApiService $employeeService
    ) { }

    public function show(Request $request): JsonResponse
    {
        try {
            $employee = $this->employeeService->getEmployeeProfile($request->user());

            return $this->successResponse(
                (new EmployeeProfileResource($employee))->resolve($request)
            );
        } catch (Exception $e) {
            $code = $e->getMessage() === 'Unauthorized.' ? 401 : 500;
            return $this->errorResponse($e->getMessage(), $code);
        }
    }

    public function uploadDocuments(EmployeeDocumentUploadRequest $request): JsonResponse
    {
        try {
            $this->employeeService->uploadDocuments($request->user(), $request);

            return $this->successResponse(
                [],
                'File uploaded successfully.'
            );
        } catch (Exception $e) {
            $code = $e->getMessage() === 'Unauthorized.' ? 401 : 500;
            return $this->errorResponse($e->getMessage(), $code);
        }
    }

    public function listDocuments(Request $request): JsonResponse
    {
        try {
            $documents = $this->employeeService->getDocuments($request->user());

            return $this->successResponse($documents);
        } catch (Exception $e) {
            $code = $e->getMessage() === 'Unauthorized.' ? 401 : 500;
            return $this->errorResponse($e->getMessage(), $code);
        }
    }
}
