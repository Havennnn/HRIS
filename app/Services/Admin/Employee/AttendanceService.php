<?php

namespace App\Services\Admin\Employee;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use PiaCore\Contracts\CrudService\ListsRecords;

class AttendanceService implements ListsRecords
{
    /**
     * @return array{
     *   tabs: array<string, array{countKey?: string|null, scope?: callable(Builder, Request): (Builder|void)}>,
     *   filters: array<string, string|callable(Builder, mixed): (Builder|void)|array{column?: string}>,
     *   sorts: array<string, string|array{column?: string}>
     * }
     */
    public function list(Model|string|Relation $model, Request $request): array
    {
        return [
            'baseQuery' => function (Builder $query): Builder {
                return $query->with([
                    'tags:id,name,value',
                    'employee:id,first_name,last_name',
                ]);
            },
            'tabs' => [
                'default' => [
                    'countKey' => 'defaultCount',
                ],
            ],
            'filters' => [
                'status' => fn (Builder $query, $value) => $query->whereHas('tags', fn (Builder $tagQuery) => $tagQuery->whereIn('value', $value)),
            ],
            'sorts' => [
                'date' => 'date',
                'status' => 'status',
                'time_in' => 'time_in',
                'time_out' => 'time_out',
                'created' => 'created_at',
            ],
        ];
    }
}
