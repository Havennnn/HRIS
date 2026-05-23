<?php

namespace App\Http\Controllers\Admin\Career;

use App\Enums\Status\CareerStatus;
use App\Http\Requests\Admin\Career\CareerRequest;
use App\Http\Resources\Admin\Career\CareerEditResource;
use App\Http\Resources\Admin\Career\CareerIndexResource;
use App\Models\Career;
use App\Models\Position;
use App\Services\Admin\Career\CareerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use PiaCore\Actions\Resource\CreateAction;
use PiaCore\Actions\Resource\DeleteAction;
use PiaCore\Actions\Resource\EditAction;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Actions\Resource\RestoreAction;
use PiaCore\Actions\Resource\StoreAction;
use PiaCore\Actions\Resource\UpdateAction;
use PiaCore\Http\Controllers\ResourceController;

final class CareerController extends ResourceController
{
    /**
     * The model class associated with the resource.
     *
     * @var class-string<\App\Models\Career>
     */
    protected string $modelClass = Career::class;

    /**
     * Service class for career resource operations.
     *
     * @var class-string<object>|null
     */
    protected ?string $serviceClass = CareerService::class;

    /**
     * Base route name for the resource.
     */
    protected ?string $routeBase = 'careers';

    /**
     * Base view path for the resource.
     */
    protected ?string $viewBase = 'Admin/Careers';

    /**
     * Display a listing of the careers.
     */
    public function index(Request $request, ListAction $action)
    {
        return $action($this->listOptions(
            request: $request,
            resource: CareerIndexResource::class,
            additionalProps: [
                'positions' => Position::options(),
            ]
        ));
    }

    /**
     * Show the create page.
     */
    public function create(Request $request, CreateAction $action)
    {
        return $action($this->createOptions(
            request: $request,
            additionalProps: [
                'positions' => Position::options(),
            ]
        ));
    }

    /**
     * Show the form for editing the specified career.
     */
    public function edit(Career $career, EditAction $action, Request $request)
    {
        return $action($this->editOptions(
            record: $career,
            request: $request,
            resource: CareerEditResource::class,
            additionalProps: [
                'positions' => Position::options(),
            ]
        ));
    }

    /**
     * Store a newly created career in storage.
     */
    public function store(CareerRequest $request, StoreAction $action)
    {
        return $action($this->storeOptions($request));
    }

    /**
     * Update the specified career in storage.
     */
    public function update(CareerRequest $request, Career $career, UpdateAction $action)
    {
        return $action($this->updateOptions($career, $request));
    }

    public function publish(Career $career): RedirectResponse
    {
        $career->update(['status' => CareerStatus::PUBLISHED]);

        return Redirect::back()->with('success', 'Career published successfully.');
    }

    public function draft(Career $career): RedirectResponse
    {
        $career->update(['status' => CareerStatus::DRAFT]);

        return Redirect::back()->with('success', 'Career moved to draft.');
    }

    /**
     * Remove the specified career from storage.
     */
    public function destroy(Career $career, DeleteAction $action, Request $request)
    {
        return $action($this->deleteOptions($career, $request));
    }

    /**
     * Restore the specified soft-deleted career.
     */
    public function restore(Career $career, RestoreAction $action, Request $request)
    {
        return $action($this->restoreOptions($career, $request));
    }
}
