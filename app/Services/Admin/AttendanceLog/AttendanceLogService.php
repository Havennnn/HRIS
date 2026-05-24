<?php

namespace App\Services\Admin\AttendanceLog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use PiaCore\Actions\Options\ListConfig;
use PiaCore\Contracts\CrudService\ListsRecords;

class AttendanceLogService implements ListsRecords
{
    /**
     * @return array{
     *   tabs: array<string, array{countKey?: string|null, scope?: callable(Builder, Request): (Builder|void)}>,
     *   filters: array<string, string|callable(Builder, mixed): (Builder|void)|array{column?: string}>,
     *   sorts: array<string, string|array{column?: string}>
     * }
     */
    public function list(Model|string|Relation $model, Request $request): ListConfig
    {
        return (new ListConfig)
            ->baseQuery(function (Builder $query, Request $request): Builder {
                return $query
                    ->when($request->filled('start_date'), fn ($q) => $q->where('date', '>=', $request->input('start_date')))
                    ->when($request->filled('end_date'), fn ($q) => $q->where('date', '<=', $request->input('end_date')));
            })
            ->tabs([
                'default' => [
                    'countKey' => 'defaultCount',
                ],
            ])
            ->filters([
                'employee' => fn (Builder $query, $value) => $query->where('employee_id', $value),
                'type' => fn (Builder $query, $value) => $query->where('type', $value),
            ])
            ->sorts([
                'timestamp' => 'timestamp',
                'type' => 'type',
                'created' => 'created_at',
            ]);
    }
}
