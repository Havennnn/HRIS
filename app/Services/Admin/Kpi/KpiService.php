<?php

namespace App\Services\Admin\Kpi;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use PiaCore\Contracts\CrudService\ListsRecords;

class KpiService implements ListsRecords
{
    public function list(Model|string|Relation $model, Request $request): array
    {
        return [
            'tabs' => [
                'default' => [
                    'countKey' => 'defaultCount',
                ],
            ],
            'filters' => [
                'name' => fn (Builder $query, $value) => $query->where('name', 'like', "%{$value}%"),
            ],
            'sorts' => [
                'name' => 'name',
                'created' => 'created_at',
            ],
        ];
    }
}
