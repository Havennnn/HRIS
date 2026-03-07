<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\DepartmentRequest;
use App\Http\Resources\Admin\DepartmentResource;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\Request;
use PiaCore\Actions\Resource\CreateAction;
use PiaCore\Actions\Resource\DeleteAction;
use PiaCore\Actions\Resource\EditAction;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Actions\Resource\RestoreAction;
use PiaCore\Actions\Resource\StoreAction;
use PiaCore\Actions\Resource\UpdateAction;
use PiaCore\Http\Controllers\ResourceController;

final class DepartmentController extends ResourceController
{
    /**
     * The model class associated with the resource.
     *
     * @var class-string<\App\Models\Department>
     */
    protected string $modelClass = Department::class;

    /**
     * The resource class used for transformation.
     *
     * @var class-string<\App\Http\Resources\Admin\DepartmentResource>|null
     */
    protected ?string $resourceClass = DepartmentResource::class;

    /**
     * Service class for department resource operations.
     *
     * @var class-string<object>|null
     */
    protected ?string $serviceClass = DepartmentService::class;

    /**
     * Base route name for the resource.
     */
    protected ?string $routeBase = 'departments';

    /**
     * Base view path for the resource.
     */
    protected ?string $viewBase = 'Admin/Departments';

    /**
     * Display a listing of the departments.
     */
    public function index(Request $request, ListAction $action)
    {
        return $action($this->listOptions(request: $request));
    }

    /**
     * Show the create page.
     */
    public function create(Request $request, CreateAction $action)
    {
        return $action($this->createOptions(request: $request));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department, EditAction $action, Request $request)
    {
        return $action($this->editOptions(
            record: $department,
            request: $request,
        ));
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(DepartmentRequest $request, StoreAction $action)
    {
        return $action($this->storeOptions($request));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(DepartmentRequest $request, Department $department, UpdateAction $action)
    {
        return $action($this->updateOptions($department, $request));
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department, DeleteAction $action, Request $request)
    {
        return $action($this->deleteOptions($department, $request));
    }

    /**
     * Restore the specified soft-deleted department.
     */
    public function restore(Department $department, RestoreAction $action, Request $request)
    {
        return $action($this->restoreOptions($department, $request));
    }
}
