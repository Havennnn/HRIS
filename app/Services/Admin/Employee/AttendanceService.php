<?php

namespace App\Services\Admin\Employee;

use App\Models\Attendance;
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
            'tabs' => [
                'default' => [
                    'countKey' => 'defaultCount',
                ],
            ],
            'filters' => [
                'employee' => fn (Builder $query, $value) => $query->where('employee_id', $value),
                'status' => fn (Builder $query, $value) => $query->where('status', $value),
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
