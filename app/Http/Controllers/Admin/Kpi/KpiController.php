<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Requests\Admin\Kpi\KpiRequest;
use App\Models\Kpi;
use App\Services\Admin\Kpi\KpiService;
use Illuminate\Http\Request;
use PiaCore\Actions\Resource\CreateAction;
use PiaCore\Actions\Resource\DeleteAction;
use PiaCore\Actions\Resource\EditAction;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Actions\Resource\RestoreAction;
use PiaCore\Actions\Resource\StoreAction;
use PiaCore\Actions\Resource\UpdateAction;
use PiaCore\Http\Controllers\ResourceController;

final class KpiController extends ResourceController
{
    protected string $modelClass = Kpi::class;

    protected ?string $serviceClass = KpiService::class;

    protected ?string $routeBase = 'settings.kpis';

    protected ?string $viewBase = 'Admin/Settings/Kpis';

    public function index(Request $request, ListAction $action)
    {
        return $action($this->listOptions(request: $request));
    }

    public function create(Request $request, CreateAction $action)
    {
        return $action($this->createOptions(request: $request));
    }

    public function edit(Kpi $kpi, EditAction $action, Request $request)
    {
        return $action($this->editOptions(record: $kpi, request: $request));
    }

    public function store(KpiRequest $request, StoreAction $action)
    {
        return $action($this->storeOptions($request));
    }

    public function update(KpiRequest $request, Kpi $kpi, UpdateAction $action)
    {
        return $action($this->updateOptions($kpi, $request));
    }

    public function destroy(Kpi $kpi, DeleteAction $action, Request $request)
    {
        return $action($this->deleteOptions($kpi, $request));
    }

    public function restore(Kpi $kpi, RestoreAction $action, Request $request)
    {
        return $action($this->restoreOptions($kpi, $request));
    }
}
