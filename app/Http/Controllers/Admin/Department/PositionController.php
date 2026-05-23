<?php

namespace App\Http\Controllers\Admin\Department;

use App\Http\Requests\Admin\Department\PositionRequest;
use App\Http\Resources\Admin\Department\PositionResource;
use App\Models\Department;
use App\Models\Position;
use App\Services\Admin\Department\PositionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use PiaCore\Actions\Resource\DeleteAction;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Actions\Resource\RestoreAction;
use PiaCore\Actions\Resource\StoreAction;
use PiaCore\Actions\Resource\UpdateAction;
use PiaCore\Http\Controllers\ResourceController;

final class PositionController extends ResourceController
{
    /**
     * The model class associated with the resource.
     *
     * @var class-string<\App\Models\Position>
     */
    protected string $modelClass = Position::class;

    /**
     * The resource class used for transformation.
     *
     * @var class-string<\App\Http\Resources\Admin\PositionResource>|null
     */
    protected ?string $resourceClass = PositionResource::class;

    /**
     * Service class for position resource operations.
     *
     * @var class-string<object>|null
     */
    protected ?string $serviceClass = PositionService::class;

    /**
     * Base route name for the resource.
     */
    protected ?string $routeBase = 'positions';

    /**
     * Base view path for the resource.
     */
    protected ?string $viewBase = 'Admin/Positions';

    /**
     * Display a listing of the positions.
     */
    public function index(Request $request, ListAction $action)
    {
        return $action($this->listOptions(
            request: $request,
            additionalProps: [
                'departments' => Department::options(),
            ]
        ));
    }

    /**
     * Show the create page.
     */
    public function create(): RedirectResponse
    {
        return Redirect::route('positions.index');
    }

    /**
     * Show the form for editing the specified position.
     */
    public function edit(): RedirectResponse
    {
        return Redirect::route('positions.index');
    }

    /**
     * Store a newly created position in storage.
     */
    public function store(PositionRequest $request, StoreAction $action)
    {
        return $action($this->storeOptions($request));
    }

    /**
     * Update the specified position in storage.
     */
    public function update(PositionRequest $request, Position $position, UpdateAction $action)
    {
        return $action($this->updateOptions($position, $request));
    }

    /**
     * Remove the specified position from storage.
     */
    public function destroy(Position $position, DeleteAction $action, Request $request)
    {
        return $action($this->deleteOptions($position, $request));
    }

    /**
     * Restore the specified soft-deleted position.
     */
    public function restore(Position $position, RestoreAction $action, Request $request)
    {
        return $action($this->restoreOptions($position, $request));
    }
}
