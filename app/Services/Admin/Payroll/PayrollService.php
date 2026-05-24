<?php

namespace App\Services\Admin\Payroll;

use App\Enums\Status\PayrollStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request as HttpRequest;
use PiaCore\Actions\Options\ListConfig;
use PiaCore\Contracts\CrudService\ListsRecords;
use PiaCore\Contracts\CrudService\ShowsRecords;

class PayrollService implements ListsRecords, ShowsRecords
{
    /**
     * @return array{
     *   tabs: array<string, array{countKey?: string|null, scope?: callable(Builder, HttpRequest): (Builder|void)}>,
     *   filters: array<string, string|callable(Builder, mixed): (Builder|void)|array{column?: string}>,
     *   sorts: array<string, string|array{column?: string}>,
     *   range: array<string, string|array{startColumn?: string, endColumn?: string}>
     * }
     */
    public function list(Model|string|Relation $model, HttpRequest $request): ListConfig
    {
        return (new ListConfig)
            ->baseQuery(function (Builder $query, HttpRequest $request): Builder {
                return $query->with([
                    'employee',
                    'employee.position',
                    'employee.position.department',
                ])
                    ->when($request->filled('start_date'), fn ($q) => $q->where('pay_period_start', '>=', $request->input('start_date')))
                    ->when($request->filled('end_date'), fn ($q) => $q->where('pay_period_end', '<=', $request->input('end_date')));
            })
            ->tabs([
                'default' => [
                    'countKey' => 'defaultCount',
                ],
                'pending' => [
                    'countKey' => 'pendingCount',
                    'scope' => fn (Builder $query) => $query->where('status', PayrollStatus::PENDING),
                ],
                'approved' => [
                    'countKey' => 'approvedCount',
                    'scope' => fn (Builder $query) => $query->where('status', PayrollStatus::APPROVED),
                ],
                'disbursed' => [
                    'countKey' => 'disbursedCount',
                    'scope' => fn (Builder $query) => $query->where('status', PayrollStatus::DISBURSED),
                ],
                'rejected' => [
                    'countKey' => 'rejectedCount',
                    'scope' => fn (Builder $query) => $query->where('status', PayrollStatus::REJECTED),
                ],
            ])
            ->filters([
                'status' => fn (Builder $query, $value) => $query->whereIn('status', $value),
                'employee' => fn (Builder $query, $value) => $query->whereIn('employee_id', $value),
            ])
            ->sorts([
                'created' => 'created_at',
                'period_start' => 'pay_period_start',
                'period_end' => 'pay_period_end',
                'net_pay' => 'net_pay',
            ])
            ->range([
                'created' => 'created_at',
                'period' => [
                    'startColumn' => 'pay_period_start',
                    'endColumn' => 'pay_period_end',
                ],
            ]);
    }

    /**
     * Show a single payroll record.
     */
    public function show(Model $record, HttpRequest $request): Model
    {
        return $record->load([
            'employee',
            'employee.position:id,name,level,department_id',
            'employee.position.department:id,name',
            'adjustments',
        ]);
    }
}
