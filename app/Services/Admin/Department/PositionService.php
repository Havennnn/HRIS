<?php

namespace App\Services\Admin\Department;

use App\Models\Position;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use PiaCore\Contracts\CrudService\ListsRecords;

class PositionService implements ListsRecords
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
                return $query->with('department:id,name');
            },
            'tabs' => [
                'default' => [
                    'countKey' => 'defaultCount',
                ],
                'archived' => [
                    'countKey' => 'archivedCount',
                    'scope' => fn (Builder $query) => $query->onlyTrashed(),
                ],
            ],
            'filters' => [
                'department' => fn (Builder $query, $value) => $query->where('department_id', $value),
                'level' => fn (Builder $query, $value) => $query->where('level', 'like', "%{$value}%"),
            ],
            'sorts' => [
                'name' => 'name',
                'salary' => 'salary',
                'level' => 'level',
                'created' => 'created_at',
            ],
        ];
    }
}
