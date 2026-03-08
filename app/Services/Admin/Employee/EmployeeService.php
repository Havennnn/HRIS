<?php

namespace App\Services\Admin\Employee;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use PiaCore\Contracts\CrudService\ListsRecords;

class EmployeeService implements ListsRecords
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
                'position' => fn (Builder $query, $value) => $query->whereIn('position_id', $value),
                'status' => fn (Builder $query, $value) => $query->whereIn('status', $value),
                'type' => fn (Builder $query, $value) => $query->whereIn('type', $value),
            ],
            'sorts' => [
                'name' => 'first_name',
                'position' => 'position_id',
                'status' => 'status',
                'type' => 'type',
                'created' => 'created_at',
            ],
        ];
    }
}
