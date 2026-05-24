<?php

namespace App\Services\Admin\Department;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use PiaCore\Actions\Options\ListConfig;
use PiaCore\Contracts\CrudService\ListsRecords;

class PositionService implements ListsRecords
{
    public function list(Model|string|Relation $model, Request $request): ListConfig
    {
        return (new ListConfig)
            ->baseQuery(function (Builder $query): Builder {
                return $query->with('department:id,name');
            })
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
                'department' => fn (Builder $query, $value) => $query->where('department_id', $value),
                'level' => fn (Builder $query, $value) => $query->where('level', 'like', "%{$value}%"),
            ])
            ->sorts([
                'name' => 'name',
                'salary' => 'salary',
                'level' => 'level',
                'created' => 'created_at',
            ]);
    }
}
