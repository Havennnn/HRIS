<?php

namespace App\Services\Admin\Holiday;

use App\Models\Holiday;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use PiaCore\Contracts\CrudService\ListsRecords;

class HolidayService implements ListsRecords
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
                'type' => fn (Builder $query, $value) => $query->whereIn('type', $value),
                'year' => fn (Builder $query, $value) => $query->whereYear('date', $value),
            ],
            'sorts' => [
                'name' => 'name',
                'date' => 'date',
                'created' => 'created_at',
            ],
            'range' => [
                'date' => 'date',
            ],
        ];
    }
}
