<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Employee;
use PiaCore\Contracts\Import\ExportStructure;

class EmployeeExport implements ExportStructure
{
    public function name(): string
    {
        return 'Employee Directory Export';
    }

    public function extension(): string
    {
        return 'csv';
    }

    public function headers(): array
    {
        return ['ID', 'First Name', 'Last Name', 'Email', 'Mobile', 'Position', 'Department', 'Status'];
    }

    public function collection(): mixed
    {
        return Employee::query()
            ->with(['position', 'position.department'])
            ->get();
    }

    public function toRow(mixed $record): array
    {
        return [
            $record->id,
            $record->first_name,
            $record->last_name,
            $record->email,
            $record->mobile_number ?? '',
            $record->position?->name ?? '',
            $record->position?->department?->name ?? '',
            $record->status?->label() ?? '',
        ];
    }

    public function filename(): string
    {
        return $this->name() . '.' . $this->extension();
    }
}
