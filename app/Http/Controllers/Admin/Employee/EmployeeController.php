<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Enums\Status\EmployeeStatus;
use App\Enums\Type\EmployeeType;
use App\Http\Requests\Admin\Employee\EmployeeRequest;
use App\Http\Requests\Admin\Employee\EmployeeDeviceRequest;
use App\Http\Resources\Admin\Employee\EmployeeEditResource;
use App\Http\Resources\Admin\Employee\EmployeeIndexResource;
use App\Models\Employee;
use App\Models\Position;
use App\Services\Admin\Employee\EmployeeService;
use Illuminate\Http\Request;
use PiaCore\Actions\Resource\CreateAction;
use PiaCore\Actions\Resource\DeleteAction;
use PiaCore\Actions\Resource\EditAction;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Actions\Resource\RestoreAction;
use PiaCore\Actions\Resource\StoreAction;
use PiaCore\Actions\Resource\UpdateAction;
use PiaCore\Http\Controllers\ResourceController;

final class EmployeeController extends ResourceController
{
    /**
     * The model class associated with the resource.
     *
     * @var class-string<\App\Models\Employee>
     */
    protected string $modelClass = Employee::class;

    /**
     * Service class for employee resource operations.
     *
     * @var class-string<object>|null
     */
    protected ?string $serviceClass = EmployeeService::class;

    /**
     * Base route name for the resource.
     */
    protected ?string $routeBase = 'employees';

    /**
     * Base view path for the resource.
     */
    protected ?string $viewBase = 'Admin/Employees';

    /**
     * Display a listing of the employees.
     */
    public function index(Request $request, ListAction $action)
    {
        return $action($this->listOptions(
            request: $request,
            resource: EmployeeIndexResource::class,
            additionalProps: [
                'positions' => Position::options(),
                'statuses' => EmployeeStatus::options(),
                'types' => EmployeeType::options(),
            ]));
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
                'types' => EmployeeType::options(),
            ]
        ));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee, EditAction $action, Request $request)
    {
        return $action($this->editOptions(
            record: $employee->load('device'),
            request: $request,
            resource: EmployeeEditResource::class,
            additionalProps: [
                'positions' => Position::options(),
                'types' => EmployeeType::options(),
            ]
        ));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(EmployeeRequest $request, StoreAction $action)
    {
        return $action($this->storeOptions($request));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(EmployeeRequest $request, Employee $employee, UpdateAction $action)
    {
        return $action($this->updateOptions($employee, $request));
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee, DeleteAction $action, Request $request)
    {
        return $action($this->deleteOptions($employee, $request));
    }

    /**
     * Restore the specified soft-deleted employee.
     */
    public function restore(Employee $employee, RestoreAction $action, Request $request)
    {
        return $action($this->restoreOptions($employee, $request));
    }

    /**
     * Update the device information for the specified employee.
     */
    public function updateDevice(EmployeeDeviceRequest $request, Employee $employee)
    {
        $this->service()->updateDevice($employee, $request->validated());

        return redirect()->back()->with('success', 'Device information updated successfully.');
    }
}
