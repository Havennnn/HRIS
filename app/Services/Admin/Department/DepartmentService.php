<?php

namespace App\Services\Admin\Department;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use PiaCore\Actions\Options\ListConfig;
use PiaCore\Contracts\CrudService\ListsRecords;

class DepartmentService implements ListsRecords
{
    public function list(Model|string|Relation $model, Request $request): ListConfig
    {
        return (new ListConfig)
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
                'name' => fn (Builder $query, $value) => $query->where('name', 'like', "%{$value}%"),
            ])
            ->sorts([
                'name' => 'name',
                'created' => 'created_at',
            ]);
    }
}
