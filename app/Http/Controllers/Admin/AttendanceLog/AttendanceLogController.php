<?php

namespace App\Http\Controllers\Admin\AttendanceLog;

use App\Enums\Type\AttendanceLogType;
use App\Exports\AttendanceLogExport;
use App\Http\Resources\Admin\AttendanceLog\AttendanceLogIndexResource;
use App\Models\AttendanceLog;
use App\Services\Admin\AttendanceLog\AttendanceLogService;
use Illuminate\Http\Request;
use PiaCore\Actions\Import\ExportAction;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Http\Controllers\ResourceController;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AttendanceLogController extends ResourceController
{
    /**
     * The model class associated with the resource.
     *
     * @var class-string<\App\Models\AttendanceLog>
     */
    protected string $modelClass = AttendanceLog::class;

    /**
     * Service class for attendance log resource operations.
     *
     * @var class-string<object>|null
     */
    protected ?string $serviceClass = AttendanceLogService::class;

    /**
     * Base route name for the resource.
     */
    protected ?string $routeBase = 'attendance-logs';

    /**
     * Base view path for the resource.
     */
    protected ?string $viewBase = 'Admin/AttendanceLogs';

    /**
     * Display a listing of the attendance logs.
     */
    public function index(Request $request, ListAction $action)
    {
        return $action($this->listOptions(
            request: $request,
            resource: AttendanceLogIndexResource::class,
            additionalProps: [
                'type' => AttendanceLogType::options(),
            ]
        ));
    }

    /**
     * Download attendance logs as CSV within a date range.
     */
    public function export(Request $request): StreamedResponse
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        return app(ExportAction::class)(
            $this->exportOptions(
                new AttendanceLogExport($request->input('start_date'), $request->input('end_date')),
                'attendance-logs-'.today()->format('Y-m-d'),
            ),
        );
    }
}
