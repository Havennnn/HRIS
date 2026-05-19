<?php

namespace App\Http\Controllers\Api\V1\Career;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Services\Api\V1\Career\CareerApiService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function __construct(
        protected CareerApiService $service
    ) {}

    public function index(Request $request): Responsable
    {
        return $this->service->list($request);
    }

    public function show(Career $career): JsonResponse
    {
        return $this->service->show($career);
    }
}
