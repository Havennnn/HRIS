<?php

namespace App\Services\Api\V1\Request;

use App\Enums\Status\RequestStatus;
use App\Http\Resources\Api\V1\Request\RequestResource;
use App\Models\Employee;
use App\Models\Request as RequestModel;
use App\Notifications\NewRequestSubmitted;
use App\Notifications\Notifier;
use App\Traits\BuildsApiResponses;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PiaCore\Models\Admin;
use Throwable;

class RequestApiService
{
    use BuildsApiResponses;

    public function list(Request $request): JsonResponse
    {
        $employee = $request->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse();
        }

        try {
            $perPage = $request->input('per_page', 15);
            $search = $request->input('search');

            $data = RequestModel::query()
                ->where('employee_id', $employee->id)
                ->search($search)
                ->orderByDesc('id')
                ->paginate($perPage);

            return $this->successResponse(
                RequestResource::collection($data)->response()->getData(true)
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve requests: '.$e->getMessage(), 500);
        }
    }

    public function show(RequestModel $request): JsonResponse
    {
        $employee = request()->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse();
        }

        if ($request->employee_id !== $employee->id) {
            return $this->errorResponse('Request not found.', 404);
        }

        try {
            $request->load('employee');

            return $this->successResponse(
                (new RequestResource($request))->resolve(request())
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve request: '.$e->getMessage(), 500);
        }
    }

    public function submit(FormRequest $httpRequest): JsonResponse
    {
        $employee = $httpRequest->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse();
        }

        try {
            $request = RequestModel::query()->create([
                'employee_id' => $employee->id,
                'type' => $httpRequest->validated('type'),
                'status' => RequestStatus::PENDING->value,
                'message' => $httpRequest->validated('message'),
                'requested_date' => $httpRequest->validated('requested_date'),
                'days' => $httpRequest->validated('days'),
                'end_date' => $httpRequest->validated('end_date'),
                'overtime_hours' => $httpRequest->validated('overtime_hours'),
            ]);

            // Notify admins about the new request
            try {
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    Notifier::notify($admin, new NewRequestSubmitted($request));
                }
            } catch (Throwable $e) {
                Log::warning('Failed to notify admins about new request', [
                    'request_id' => $request->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return $this->successResponse(
                (new RequestResource($request))->resolve($httpRequest),
                'Request submitted successfully.',
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to submit request: '.$e->getMessage(), 422);
        }
    }
}
