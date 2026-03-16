<?php

namespace App\Services\Admin\Career;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use PiaCore\Contracts\CrudService\ListsRecords;

class CareerService implements ListsRecords
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
            'baseQuery' => fn (Builder $query) => $query->with(['position']),
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
                'is_active' => fn (Builder $query, $value) => $query->where('is_active', $value),
            ],
            'sorts' => [
                'created' => 'created_at',
            ],
            'range' => [
                'created' => 'created_at',
            ],
        ];
    }
}
