<?php

namespace App\Services\Admin\PerformanceReview;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PiaCore\Contracts\CrudService\ListsRecords;
use PiaCore\Contracts\CrudService\StoresRecords;
use PiaCore\Contracts\CrudService\UpdatesRecords;

class PerformanceReviewService implements ListsRecords, StoresRecords, UpdatesRecords
{
    public function list(Model|string|Relation $model, Request $request): array
    {
        return [
            'baseQuery' => fn (Builder $query) => $query->with(['employee', 'reviewer']),
            'tabs' => [
                'default' => [
                    'countKey' => 'defaultCount',
                ],
            ],
            'filters' => [
                'employee' => fn (Builder $query, $value) => $query->whereIn('employee_id', $value),
                'reviewer' => fn (Builder $query, $value) => $query->whereIn('reviewer_id', $value),
                'status' => fn (Builder $query, $value) => $query->whereIn('status', $value),
            ],
            'sorts' => [
                'review_date' => 'review_date',
                'status' => 'status',
                'created' => 'created_at',
            ],
            'range' => [
                'review_date' => 'review_date',
            ],
        ];
    }

    public function store(string $modelClass, array $payload, ?FormRequest $request = null): Model
    {
        return DB::transaction(function () use ($modelClass, $request) {
            $record = $modelClass::query()->create(
                $this->prepareStoreData($request)
            );

            $this->syncScores($record, $request);
            $this->syncFeedback($record, $request);

            return $record;
        });
    }

    public function update(Model $record, array $payload, ?FormRequest $request = null): Model
    {
        return DB::transaction(function () use ($record, $request) {
            $record->update(
                $this->prepareUpdateData($record, $request)
            );

            $this->syncScores($record, $request);
            $this->syncFeedback($record, $request);

            return $record->fresh(['reviewScores', 'reviewFeedback']);
        });
    }

    protected function prepareStoreData(?FormRequest $request = null): array
    {
        return [
            'employee_id' => $request?->validated('employee_id'),
            'reviewer_id' => $request?->validated('reviewer_id'),
            'review_date' => $request?->validated('review_date'),
            'status' => $request?->validated('status'),
        ];
    }

    protected function prepareUpdateData(Model $record, ?FormRequest $request = null): array
    {
        return [
            'employee_id' => $request?->validated('employee_id'),
            'reviewer_id' => $request?->validated('reviewer_id'),
            'review_date' => $request?->validated('review_date'),
            'status' => $request?->validated('status'),
        ];
    }

    protected function syncScores(Model $record, ?FormRequest $request = null): void
    {
        $scores = $request?->validated('scores', []);

        $existingIds = collect($scores)->pluck('id')->filter()->all();
        $record->reviewScores()->whereNotIn('id', $existingIds)->delete();

        foreach ($scores as $score) {
            $record->reviewScores()->updateOrCreate(
                ['id' => $score['id'] ?? null],
                [
                    'kpi_id' => $score['kpi_id'],
                    'score' => $score['score'],
                ]
            );
        }
    }

    protected function syncFeedback(Model $record, ?FormRequest $request = null): void
    {
        $feedback = $request?->validated('feedback', []);

        $existingIds = collect($feedback)->pluck('id')->filter()->all();
        $record->reviewFeedback()->whereNotIn('id', $existingIds)->delete();

        foreach ($feedback as $item) {
            $record->reviewFeedback()->updateOrCreate(
                ['id' => $item['id'] ?? null],
                [
                    'type' => $item['type'],
                    'feedback' => $item['feedback'],
                ]
            );
        }
    }
}
