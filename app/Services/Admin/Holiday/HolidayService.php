<?php

namespace App\Services\Admin\Holiday;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use PiaCore\Actions\Options\ListConfig;
use PiaCore\Contracts\CrudService\ListsRecords;

class HolidayService implements ListsRecords
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
                'type' => fn (Builder $query, $value) => $query->whereIn('type', $value),
                'year' => fn (Builder $query, $value) => $query->whereYear('date', $value),
            ])
            ->sorts([
                'name' => 'name',
                'date' => 'date',
                'created' => 'created_at',
            ])
            ->range([
                'date' => 'date',
            ]);
    }
}
