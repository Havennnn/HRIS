<?php

namespace App\Services\Admin\Employee;

use App\Models\Employee;
use App\Models\EmployeeDevice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'baseQuery' => function (Builder $query): Builder {
                return $query->with(['position', 'position.department', 'device']);
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
                'position' => fn (Builder $query, $value) => $query->whereIn('position_id', $value),
                'status' => fn (Builder $query, $value) => $query->whereIn('status', $value),
                'type' => fn (Builder $query, $value) => $query->whereIn('type', $value),
            ],
            'sorts' => [
                'name' => 'first_name',
                'created' => 'created_at',
            ],
        ];
    }

    /**
     * Update the device information for an employee.
     *
     * @param  Employee  $employee
     * @param  array  $payload
     * @return EmployeeDevice
     */
    public function updateDevice(Employee $employee, array $payload): EmployeeDevice
    {
        return DB::transaction(function () use ($employee, $payload) {
            // Extract device data from payload
            $deviceData = $this->extractDeviceData($payload);

            // Update or create the device record
            return $employee->device()->updateOrCreate(
                [], // Empty array means we update the existing device or create if none exists
                $deviceData
            );
        });
    }

    /**
     * Extract device data from the payload.
     *
     * @param  array  $payload
     * @return array
     */
    protected function extractDeviceData(array $payload): array
    {
        return [
            'desktop' => $payload['desktop'] ?? '',
            'laptop' => $payload['laptop'] ?? '',
        ];
    }
}
