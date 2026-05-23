<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

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
     * GET /admin/dropdowns/{class}
     *
     * Example: GET /admin/dropdowns/App\Enums\Status\EmployeeStatus?search=active
     */
    public function __invoke(Request $request, string $class): JsonResponse
    {
        $options = $this->service->get($class, $request);

        return response()->json($options);
    }
}
