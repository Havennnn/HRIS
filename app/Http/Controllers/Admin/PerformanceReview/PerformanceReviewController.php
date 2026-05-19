<?php

namespace App\Http\Controllers\Admin\PerformanceReview;

use App\Enums\Status\PerformanceReviewStatus;
use App\Http\Requests\Admin\PerformanceReview\PerformanceReviewRequest;
use App\Models\Employee;
use App\Models\Kpi;
use App\Models\PerformanceReview;
use App\Services\Admin\PerformanceReview\PerformanceReviewService;
use Illuminate\Http\Request;
use PiaCore\Actions\Resource\CreateAction;
use PiaCore\Actions\Resource\EditAction;
use PiaCore\Actions\Resource\ListAction;
use PiaCore\Actions\Resource\StoreAction;
use PiaCore\Actions\Resource\UpdateAction;
use PiaCore\Http\Controllers\ResourceController;

final class PerformanceReviewController extends ResourceController
{
    protected string $modelClass = PerformanceReview::class;

    protected ?string $serviceClass = PerformanceReviewService::class;

    protected ?string $routeBase = 'performance-reviews';

    protected ?string $viewBase = 'Admin/PerformanceReviews';

    public function index(Request $request, ListAction $action)
    {
        return $action($this->listOptions(
            request: $request,
            additionalProps: [
                'statuses' => PerformanceReviewStatus::options(),
            ]
        ));
    }

    public function create(Request $request, CreateAction $action)
    {
        return $action($this->createOptions(
            request: $request,
            additionalProps: [
                'employees' => Employee::query()->where('status', 1)->get(['id', 'first_name', 'last_name']),
                'kpis' => Kpi::all(['id', 'name']),
            ]
        ));
    }

    public function edit(PerformanceReview $performanceReview, EditAction $action, Request $request)
    {
        return $action($this->editOptions(
            record: $performanceReview->load(['reviewScores', 'reviewFeedback']),
            request: $request,
            additionalProps: [
                'employees' => Employee::query()->where('status', 1)->get(['id', 'first_name', 'last_name']),
                'kpis' => Kpi::all(['id', 'name']),
                'statuses' => PerformanceReviewStatus::options(),
            ]
        ));
    }

    public function store(PerformanceReviewRequest $request, StoreAction $action)
    {
        return $action($this->storeOptions($request));
    }

    public function update(PerformanceReviewRequest $request, PerformanceReview $performanceReview, UpdateAction $action)
    {
        return $action($this->updateOptions($performanceReview, $request));
    }
}
