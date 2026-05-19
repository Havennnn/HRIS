<?php

namespace App\Http\Controllers\Api\V1\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Services\Api\V1\Payroll\PayrollApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollApiService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        return $this->service->list($request);
    }

    public function show(Payroll $payroll, Request $request): JsonResponse
    {
        return $this->service->show($payroll, $request);
    }

    public function latest(Request $request): JsonResponse
    {
        return $this->service->latest($request);
    }
}
