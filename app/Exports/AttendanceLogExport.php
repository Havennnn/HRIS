<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Attendance;
use PiaCore\Contracts\Import\ExportStructure;

class AttendanceLogExport implements ExportStructure
{
    public function __construct(
        protected string $startDate,
        protected string $endDate,
    ) {}

    public function name(): string
    {
        return 'Attendance Log Export';
    }

    public function extension(): string
    {
        return 'csv';
    }

    public function headers(): array
    {
        return ['Employee', 'Date', 'Time In', 'Time Out', 'Status', 'Late Mins'];
    }

    public function collection(): mixed
    {
        return Attendance::query()
            ->with(['employee'])
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date')
            ->orderBy('employee_id')
            ->get();
    }

    public function toRow(mixed $record): array
    {
        return [
            $record->employee?->full_name ?? 'Unknown',
            $record->date->format('Y-m-d'),
            $record->time_in?->format('H:i') ?? '',
            $record->time_out?->format('H:i') ?? '',
            $record->status?->label() ?? '',
            (string) ($record->late_minutes ?? 0),
        ];
    }

    public function filename(): string
    {
        return $this->name() . '.' . $this->extension();
    }
}
