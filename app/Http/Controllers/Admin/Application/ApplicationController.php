<?php

namespace App\Http\Controllers\Admin\Application;

use App\Enums\Status\ApplicationStatus;
use App\Http\Resources\Admin\Application\ApplicationIndexResource;
use App\Http\Resources\Admin\Application\ApplicationShowResource;
use App\Models\Application;
use App\Models\Position;
use App\Services\Admin\Application\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use PiaCore\Actions\Resource\DeleteAction;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Actions\Resource\RestoreAction;
use PiaCore\Actions\Resource\ShowAction;
use PiaCore\Actions\Import\ExportAction;
use PiaCore\Enums\ExportType;
use PiaCore\Http\Controllers\ResourceController;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ApplicationController extends ResourceController
{
    /**
     * The model class associated with the resource.
     *
     * @var class-string<\App\Models\Application>
     */
    protected string $modelClass = Application::class;

    /**
     * Service class for application resource operations.
     *
     * @var class-string<object>|null
     */
    protected ?string $serviceClass = ApplicationService::class;

    /**
     * Base route name for the resource.
     */
    protected ?string $routeBase = 'applications';

    /**
     * Base view path for the resource.
     */
    protected ?string $viewBase = 'Admin/Applications';

    /**
     * Display a listing of the applications.
     */
    public function index(Request $request, ListAction $action)
    {
        return $action($this->listOptions(
            request: $request,
            resource: ApplicationIndexResource::class,
            additionalProps: [
                'positions' => Position::options(),
                'statuses' => ApplicationStatus::options(),
            ]
        ));
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application, ShowAction $action, Request $request)
    {
        return $action($this->showOptions(
            record: $application,
            request: $request,
            resource: ApplicationShowResource::class,
        ));
    }

    // ─── Export ─────────────────────────────────────────────────────

    /**
     * Download applications as CSV.
     */
    public function export(Request $request, ExportAction $action): StreamedResponse
    {
        return $action(
            $this->exportOptions(
                resource: ApplicationIndexResource::class,
                filename: 'applications-'.today()->format('Y-m-d'),
                type: ExportType::CSV,
                request: $request,
            ),
        );
    }

    // ─── Status Transitions ─────────────────────────────────────────

    public function interview(Application $application): RedirectResponse|Redirector
    {
        try {
            $this->service()->interview($application);

            return redirect()->back()->with('success', 'Application moved to interview successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(Application $application): RedirectResponse|Redirector
    {
        try {
            $this->service()->reject($application);

            return redirect()->back()->with('success', 'Application rejected successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function hire(Application $application): RedirectResponse|Redirector
    {
        try {
            $this->service()->hire($application);

            return redirect()->back()->with('success', 'Application hired successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified application from storage.
     */
    public function destroy(Application $application, DeleteAction $action, Request $request)
    {
        return $action($this->deleteOptions($application, $request));
    }

    /**
     * Restore the specified soft-deleted application.
     */
    public function restore(Application $application, RestoreAction $action, Request $request)
    {
        return $action($this->restoreOptions($application, $request));
    }
}
