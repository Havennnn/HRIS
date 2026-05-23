<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Services\Admin\DropdownService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DropdownController extends Controller
{
    public function __construct(
        protected DropdownService $service
    ) {}

    /**
     * GET /api/v1/dropdowns/{class}
     *
     * Example: GET /api/v1/dropdowns/App\Enums\Status\EmployeeStatus
     */
    public function __invoke(Request $request, string $class): JsonResponse
    {
        $options = $this->service->get($class, $request);

        return response()->json($options);
    }
}
