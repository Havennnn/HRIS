<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Enums\Status\EmployeeStatus;
use App\Enums\Type\EmployeeContactType;
use App\Enums\Type\EmployeeType;
use App\Exports\EmployeeExport;
use App\Http\Requests\Admin\Employee\EmployeeContactRequest;
use App\Http\Requests\Admin\Employee\EmployeeDeviceRequest;
use App\Http\Requests\Admin\Employee\EmployeeImportRequest;
use App\Http\Requests\Admin\Employee\EmployeeRequest;
use App\Http\Resources\Admin\Employee\EmployeeEditResource;
use App\Http\Resources\Admin\Employee\EmployeeIndexResource;
use App\Imports\EmployeeImport;
use App\Manifests\EmployeeManifest;
use App\Models\Employee;
use App\Models\Position;
use App\Services\Admin\Employee\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PiaCore\Actions\Import\ExportAction;
use PiaCore\Actions\Import\ImportAction;
use PiaCore\Actions\Import\ManifestAction;
use PiaCore\Actions\Resource\CreateAction;
use PiaCore\Actions\Resource\DeleteAction;
use PiaCore\Actions\Resource\EditAction;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Actions\Resource\RestoreAction;
use PiaCore\Actions\Resource\StoreAction;
use PiaCore\Actions\Resource\UpdateAction;
use PiaCore\Enums\ExportType;
use PiaCore\Http\Controllers\ResourceController;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class EmployeeController extends ResourceController
{
    /**
     * The model class associated with the resource.
     *
     * @var class-string<Employee>
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
    public function index(Request $request, ListAction $action): mixed
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
    public function create(Request $request, CreateAction $action): mixed
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
    public function edit(Employee $employee, EditAction $action, Request $request): mixed
    {
        return $action($this->editOptions(
            record: $employee->load([
                'contact',
                'device',
                'documents.sssFile',
                'documents.philhealthFile',
                'documents.birFile',
                'documents.medicalFile',
            ]),
            request: $request,
            resource: EmployeeEditResource::class,
            additionalProps: [
                'positions' => Position::options(),
                'types' => EmployeeType::options(),
                'contactTypes' => EmployeeContactType::options(),
            ]
        ));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(EmployeeRequest $request, StoreAction $action): mixed
    {
        return $action($this->storeOptions($request));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(EmployeeRequest $request, Employee $employee, UpdateAction $action): mixed
    {
        return $action($this->updateOptions($employee, $request));
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee, DeleteAction $action, Request $request): mixed
    {
        return $action($this->deleteOptions($employee, $request));
    }

    /**
     * Restore the specified soft-deleted employee.
     */
    public function restore(Employee $employee, RestoreAction $action, Request $request): mixed
    {
        return $action($this->restoreOptions($employee, $request));
    }

    /**
     * Send a password reset link to the employee's email.
     */
    public function resetPassword(Employee $employee): RedirectResponse
    {
        return $this->service()->resetPassword($employee);
    }

    /**
     * Update the device information for the specified employee.
     */
    public function updateDevice(EmployeeDeviceRequest $request, Employee $employee): RedirectResponse
    {
        return $this->service()->updateDevice($employee, $request);
    }

    /**
     * Update the contact person information for the specified employee.
     */
    public function updateContactPerson(EmployeeContactRequest $request, Employee $employee): RedirectResponse
    {
        return $this->service()->updateContactPerson($employee, $request);
    }

    // ─── Import / Export ─────────────────────────────────────────────

    /**
     * Download a XLSX template (manifest) showing required columns for import.
     */
    public function manifest(ManifestAction $action): StreamedResponse
    {
        return $action(
            $this->manifestOptions(EmployeeManifest::class, 'employee-manifest-'.today()->format('Y-m-d')),
        );
    }

    /**
     * Process the uploaded CSV file for employee import.
     */
    public function import(EmployeeImportRequest $request): RedirectResponse
    {
        return app(ImportAction::class)(
            request: $request,
            handler: new EmployeeImport(),
        );
    }

    /**
     * Download employees as XLSX.
     */
    public function export(ExportAction $action): StreamedResponse
    {
        return $action(
            $this->exportOptions(EmployeeExport::class, 'employees-'.today()->format('Y-m-d'), ExportType::XLSX),
        );
    }
}
