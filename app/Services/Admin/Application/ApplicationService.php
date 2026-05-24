<?php

namespace App\Services\Admin\Application;

use App\Enums\Status\ApplicationStatus;
use App\Models\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PiaCore\Contracts\CrudService\DeletesRecords;
use PiaCore\Contracts\CrudService\ListsRecords;
use PiaCore\Contracts\CrudService\RestoresRecords;
use PiaCore\Actions\Options\ListConfig;
use PiaCore\Contracts\CrudService\ShowsRecords;

class ApplicationService implements DeletesRecords, ListsRecords, RestoresRecords, ShowsRecords
{
    /**
     * @return array{
     *   tabs: array<string, array{countKey?: string|null, scope?: callable(Builder, HttpRequest): (Builder|void)}>,
     *   filters: array<string, string|callable(Builder, mixed): (Builder|void)|array{column?: string}>,
     *   sorts: array<string, string|array{column?: string}>
     * }
     */
    public function list(Model|string|Relation $model, HttpRequest $request): ListConfig
    {
        return (new ListConfig)
            ->baseQuery(fn (Builder $query) => $query->with(['career']))
            ->tabs([
                'default' => [
                    'countKey' => 'defaultCount',
                ],
                'archived' => [
                    'countKey' => 'archivedCount',
                    'scope' => fn (Builder $query) => $query->onlyTrashed(),
                ],
            ])
            ->filters([
                'status' => fn (Builder $query, $value) => $query->whereIn('status', $value),
                'position' => fn (Builder $query, $value) => $query->whereIn('career_id', $value),
            ])
            ->sorts([
                'created' => 'created_at',
            ])
            ->range([
                'created' => 'created_at',
            ]);
    }

    /**
     * Configure the query for showing a single record.
     */
    public function show(Model $record, HttpRequest $request): Model
    {
        if ($record instanceof Application && $record->status === ApplicationStatus::PENDING) {
            $record->update(['status' => ApplicationStatus::REVIEWING]);
            $record->refresh();
        }

        return $record->load(['career', 'details', 'interview']);
    }

    public function interview(Application $application): Application
    {
        if ($application->status !== ApplicationStatus::REVIEWING) {
            throw new InvalidArgumentException('Only reviewing applications can be moved to interview.');
        }

        return DB::transaction(function () use ($application) {
            $application->update(['status' => ApplicationStatus::INTERVIEW]);

            return $application->fresh();
        });
    }

    public function reject(Application $application): Application
    {
        if (! in_array($application->status, [ApplicationStatus::REVIEWING, ApplicationStatus::INTERVIEW], true)) {
            throw new InvalidArgumentException('Only reviewing or interview-stage applications can be rejected.');
        }

        return DB::transaction(function () use ($application) {
            $application->update(['status' => ApplicationStatus::REJECTED]);

            return $application->fresh();
        });
    }

    public function hire(Application $application): Application
    {
        if ($application->status !== ApplicationStatus::INTERVIEW) {
            throw new InvalidArgumentException('Only interview-stage applications can be hired.');
        }

        return DB::transaction(function () use ($application) {
            $application->update(['status' => ApplicationStatus::HIRED]);

            return $application->fresh();
        });
    }

    /**
     * Handle deletion of a record.
     */
    public function delete(Model $record, HttpRequest $request): void
    {
        $record->delete();
    }

    /**
     * Handle restoration of a soft-deleted record.
     */
    public function restore(Model $record, HttpRequest $request): Model
    {
        $record->restore();

        return $record;
    }
}
