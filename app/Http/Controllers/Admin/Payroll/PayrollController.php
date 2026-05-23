<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Enums\Status\PayrollStatus;
use App\Exports\PayrollExport;
use App\Http\Resources\Admin\Payroll\PayrollIndexResource;
use App\Http\Resources\Admin\Payroll\PayrollShowResource;
use App\Models\Payroll;
use App\Services\Admin\Payroll\PayrollService;
use Illuminate\Http\Request as HttpRequest;
use PiaCore\Actions\Import\ExportAction;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Actions\Resource\ShowAction;
use PiaCore\Http\Controllers\ResourceController;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PayrollController extends ResourceController
{
    /**
     * The model class associated with the resource.
     *
     * @var class-string<\App\Models\Payroll>
     */
    protected string $modelClass = Payroll::class;

    /**
     * Service class for payroll resource operations.
     *
     * @var class-string<object>|null
     */
    protected ?string $serviceClass = PayrollService::class;

    /**
     * Base route name for the resource.
     */
    protected ?string $routeBase = 'payrolls';

    /**
     * Base view path for the resource.
     */
    protected ?string $viewBase = 'Admin/Payrolls';

    /**
     * Display a listing of payrolls.
     */
    public function index(HttpRequest $request, ListAction $action)
    {
        return $action($this->listOptions(
            request: $request,
            resource: PayrollIndexResource::class,
            additionalProps: [
                'statuses' => PayrollStatus::options(),
            ]
        ));
    }

    /**
     * Display the specified payroll.
     */
    public function show(Payroll $payroll, ShowAction $action, HttpRequest $request)
    {
        return $action($this->showOptions(
            record: $payroll,
            request: $request,
            resource: PayrollShowResource::class
        ));
    }

    /**
     * Download payrolls as CSV within a date range.
     */
    public function export(HttpRequest $request, ExportAction $action): StreamedResponse
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        return $action(
            $this->exportOptions(
                new PayrollExport($request->input('start_date'), $request->input('end_date')),
                'payroll-'.today()->format('Y-m-d'),
            ),
        );
    }
}
