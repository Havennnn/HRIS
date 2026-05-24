<?php

namespace App\Http\Controllers\Admin\Request;

use App\Enums\Status\RequestStatus;
use App\Enums\Type\RequestType;
use App\Http\Resources\Admin\Request\RequestIndexResource;
use App\Http\Resources\Admin\Request\RequestShowResource;
use App\Models\Request as RequestModel;
use App\Services\Admin\Request\RequestService;
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

final class RequestController extends ResourceController
{
    /**
     * The model class associated with the resource.
     *
     * @var class-string<\App\Models\Request>
     */
    protected string $modelClass = RequestModel::class;

    /**
     * Service class for request resource operations.
     *
     * @var class-string<object>|null
     */
    protected ?string $serviceClass = RequestService::class;

    /**
     * Base route name for the resource.
     */
    protected ?string $routeBase = 'requests';

    /**
     * Base view path for the resource.
     */
    protected ?string $viewBase = 'Admin/Requests';

    /**
     * Display a listing of the requests.
     */
    public function index(Request $request, ListAction $action)
    {
        return $action($this->listOptions(
            request: $request,
            resource: RequestIndexResource::class,
            additionalProps: [
                'types' => RequestType::options(),
                'statuses' => RequestStatus::options(),
            ]
        ));
    }

    /**
     * Display the specified request.
     */
    public function show(RequestModel $request, ShowAction $action, Request $httpRequest)
    {
        return $action($this->showOptions(
            record: $request,
            request: $httpRequest,
            resource: RequestShowResource::class
        ));
    }

    /**
     * Approve the specified request.
     */
    public function approve(RequestModel $request): RedirectResponse|Redirector
    {
        try {
            $service = app(RequestService::class);
            $service->approve($request);

            return redirect()->back()->with('success', 'Request approved successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject the specified request.
     */
    public function reject(RequestModel $request): RedirectResponse|Redirector
    {
        try {
            $service = app(RequestService::class);
            $service->reject($request);

            return redirect()->back()->with('success', 'Request rejected successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel the specified request.
     */
    public function cancel(RequestModel $request): RedirectResponse|Redirector
    {
        try {
            $service = app(RequestService::class);
            $service->cancel($request);

            return redirect()->back()->with('success', 'Request cancelled successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Complete the specified approved request.
     */
    public function complete(RequestModel $request): RedirectResponse|Redirector
    {
        try {
            $service = app(RequestService::class);
            $service->complete($request);

            return redirect()->back()->with('success', 'Request completed successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ─── Export ─────────────────────────────────────────────────────

    /**
     * Download requests as CSV.
     */
    public function export(Request $request, ExportAction $action): StreamedResponse
    {
        return $action(
            $this->exportOptions(
                resource: RequestIndexResource::class,
                filename: 'requests-'.today()->format('Y-m-d'),
                type: ExportType::CSV,
                request: $request,
            ),
        );
    }

    /**
     * Remove the specified request from storage (archive).
     */
    public function destroy(RequestModel $request, DeleteAction $action, Request $httpRequest)
    {
        return $action($this->deleteOptions($request, $httpRequest));
    }

    /**
     * Restore the specified soft-deleted request.
     */
    public function restore(RequestModel $request, RestoreAction $action, Request $httpRequest)
    {
        return $action($this->restoreOptions($request, $httpRequest));
    }
}
