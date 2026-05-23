<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Payroll;
use PiaCore\Contracts\Import\ExportStructure;

class PayrollExport implements ExportStructure
{
    public function __construct(
        protected string $startDate,
        protected string $endDate,
    ) {}

    public function name(): string
    {
        return 'Payroll Export';
    }

    public function extension(): string
    {
        return 'csv';
    }

    public function headers(): array
    {
        return ['Employee', 'Period', 'Gross', 'SSS', 'Pag-IBIG', 'PhilHealth', 'Tax', 'Net'];
    }

    public function collection(): mixed
    {
        return Payroll::query()
            ->with(['employee'])
            ->whereBetween('pay_period_start', [$this->startDate, $this->endDate])
            ->orderBy('pay_period_start')
            ->get();
    }

    public function toRow(mixed $record): array
    {
        return [
            $record->employee?->full_name ?? 'Unknown',
            $record->pay_period_start->format('M d, Y') . ' - ' . $record->pay_period_end->format('M d, Y'),
            number_format((float) $record->gross_pay, 2),
            number_format((float) $record->sss, 2),
            number_format((float) $record->pagibig, 2),
            number_format((float) $record->philhealth, 2),
            number_format((float) $record->tax, 2),
            number_format((float) $record->net_pay, 2),
        ];
    }

    public function filename(): string
    {
        return $this->name() . '.' . $this->extension();
    }
}
