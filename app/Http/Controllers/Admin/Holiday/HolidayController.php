<?php

namespace App\Http\Controllers\Admin\Holiday;

use App\Enums\Type\HolidayType;
use App\Http\Requests\Admin\Holiday\HolidayRequest;
use App\Http\Resources\Admin\Holiday\HolidayEditResource;
use App\Http\Resources\Admin\Holiday\HolidayIndexResource;
use App\Models\Holiday;
use App\Services\Admin\Holiday\HolidayService;
use Illuminate\Http\Request;
use PiaCore\Actions\Resource\CreateAction;
use PiaCore\Actions\Resource\DeleteAction;
use PiaCore\Actions\Resource\EditAction;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Actions\Resource\RestoreAction;
use PiaCore\Actions\Resource\StoreAction;
use PiaCore\Actions\Resource\UpdateAction;
use PiaCore\Http\Controllers\ResourceController;

final class HolidayController extends ResourceController
{
    /**
     * The model class associated with the resource.
     *
     * @var class-string<\App\Models\Holiday>
     */
    protected string $modelClass = Holiday::class;

    /**
     * Service class for holiday resource operations.
     *
     * @var class-string<object>|null
     */
    protected ?string $serviceClass = HolidayService::class;

    /**
     * Base route name for the resource.
     */
    protected ?string $routeBase = 'holidays';

    /**
     * Base view path for the resource.
     */
    protected ?string $viewBase = 'Admin/Holidays';

    /**
     * Display a listing of the holidays.
     */
    public function index(Request $request, ListAction $action)
    {
        return $action($this->listOptions(
            request: $request,
            resource: HolidayIndexResource::class,
            additionalProps: [
                'types' => HolidayType::options(),
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
                'types' => HolidayType::options(),
            ]
        ));
    }

    /**
     * Show the form for editing the specified holiday.
     */
    public function edit(Holiday $holiday, EditAction $action, Request $request)
    {
        return $action($this->editOptions(
            record: $holiday,
            request: $request,
            resource: HolidayEditResource::class,
            additionalProps: [
                'types' => HolidayType::options(),
            ]
        ));
    }

    /**
     * Store a newly created holiday in storage.
     */
    public function store(HolidayRequest $request, StoreAction $action)
    {
        return $action($this->storeOptions($request));
    }

    /**
     * Update the specified holiday in storage.
     */
    public function update(HolidayRequest $request, Holiday $holiday, UpdateAction $action)
    {
        return $action($this->updateOptions($holiday, $request));
    }

    /**
     * Remove the specified holiday from storage.
     */
    public function destroy(Holiday $holiday, DeleteAction $action, Request $request)
    {
        return $action($this->deleteOptions($holiday, $request));
    }

    /**
     * Restore the specified soft-deleted holiday.
     */
    public function restore(Holiday $holiday, RestoreAction $action, Request $request)
    {
        return $action($this->restoreOptions($holiday, $request));
    }
}
