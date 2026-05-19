<?php

namespace App\Services\Api\V1\Payroll;

use App\Http\Resources\Api\V1\Payroll\PayrollResource;
use App\Models\Employee;
use App\Models\Payroll;
use App\Traits\BuildsApiResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollApiService
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

            $data = Payroll::query()
                ->with('adjustments')
                ->where('employee_id', $employee->id)
                ->search($search)
                ->orderByDesc('pay_period_end')
                ->orderByDesc('id')
                ->paginate($perPage);

            return $this->successResponse(
                PayrollResource::collection($data)->response()->getData(true)
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve payroll list: '.$e->getMessage(), 500);
        }
    }

    public function show(Payroll $payroll, Request $request): JsonResponse
    {
        $employee = $request->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse();
        }

        if ($payroll->employee_id !== $employee->id) {
            return $this->errorResponse('Payroll not found.', 404);
        }

        try {
            $payroll->load('adjustments');

            return $this->successResponse(
                (new PayrollResource($payroll))->resolve($request)
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve payroll: '.$e->getMessage(), 500);
        }
    }

    public function latest(Request $request): JsonResponse
    {
        $employee = $request->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse();
        }

        try {
            $payroll = Payroll::query()
                ->with('adjustments')
                ->where('employee_id', $employee->id)
                ->orderByDesc('pay_period_end')
                ->orderByDesc('id')
                ->first();

            if (! $payroll) {
                return $this->errorResponse('No payroll records found.', 404);
            }

            return $this->successResponse(
                (new PayrollResource($payroll))->resolve($request)
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve latest payroll: '.$e->getMessage(), 500);
        }
    }
}
