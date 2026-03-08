<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Enums\Status\AttendanceStatus;
use App\Http\Resources\Admin\Employee\Attendance\AttendanceIndexResource;
use App\Http\Resources\Admin\Employee\EmployeeHeaderResource;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\Admin\Employee\AttendanceService;
use Illuminate\Http\Request;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Http\Controllers\ResourceController;

final class AttendanceController extends ResourceController
{
    /**
     * The model class associated with the resource.
     *
     * @var class-string<\App\Models\Attendance>
     */
    protected string $modelClass = Attendance::class;

    /**
     * Service class for attendance resource operations.
     *
     * @var class-string<object>|null
     */
    protected ?string $serviceClass = AttendanceService::class;

    /**
     * Base route name for the resource.
     */
    protected ?string $routeBase = 'employees.attendances';

    /**
     * Base view path for the resource.
     */
    protected ?string $viewBase = 'Admin/Employees/Attendances';

    /**
     * Display a listing of the attendances.
     */
    public function index(Request $request, Employee $employee, ListAction $action)
    {
        return $action($this->listOptions(
            request: $request,
            resource: AttendanceIndexResource::class,
            additionalProps: [
                'employee' => new EmployeeHeaderResource($employee),
                'status' => AttendanceStatus::options(),
            ],
        ));
    }
}
