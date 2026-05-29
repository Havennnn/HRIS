<?php

declare(strict_types=1);

namespace App\Services\Admin\ImportExport;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Support\Facades\DB;

class ExportService
{
    /**
     * Export employees as a CSV string.
     *
     * @return string
     */
    public function exportEmployees(): string
    {
        $headers = ['ID', 'First Name', 'Last Name', 'Email', 'Mobile', 'Position', 'Department', 'Status'];

        $rows = collect();

        Employee::query()
            ->with(['position', 'position.department'])
            ->chunk(200, function ($employees) use (&$rows) {
                foreach ($employees as $employee) {
                    $rows->push([
                        $employee->id,
                        $employee->first_name,
                        $employee->last_name,
                        $employee->email,
                        $employee->mobile_number ?? '',
                        $employee->position?->name ?? '',
                        $employee->position?->department?->name ?? '',
                        $employee->status?->label() ?? '',
                    ]);
                }
            });

        return $this->toCsv($headers, $rows->toArray());
    }

    /**
     * Export payrolls within a date range as a CSV string.
     *
     * @return string
     */
    public function exportPayrolls(string $startDate, string $endDate): string
    {
        $headers = ['Employee', 'Period', 'Gross', 'SSS', 'Pag-IBIG', 'PhilHealth', 'Tax', 'Net'];

        $rows = collect();

        Payroll::query()
            ->with(['employee'])
            ->whereBetween('pay_period_start', [$startDate, $endDate])
            ->orderBy('pay_period_start')
            ->chunk(200, function ($payrolls) use (&$rows) {
                foreach ($payrolls as $payroll) {
                    $rows->push([
                        $payroll->employee?->full_name ?? 'Unknown',
                        $payroll->pay_period_start->format('M d, Y') . ' - ' . $payroll->pay_period_end->format('M d, Y'),
                        number_format((float) $payroll->gross_pay, 2),
                        number_format((float) $payroll->sss, 2),
                        number_format((float) $payroll->pagibig, 2),
                        number_format((float) $payroll->philhealth, 2),
                        number_format((float) $payroll->tax, 2),
                        number_format((float) $payroll->net_pay, 2),
                    ]);
                }
            });

        return $this->toCsv($headers, $rows->toArray());
    }

    /**
     * Export attendance logs within a date range as a CSV string.
     *
     * @return string
     */
    public function exportAttendanceLogs(string $startDate, string $endDate): string
    {
        $headers = ['Employee', 'Date', 'Time In', 'Time Out', 'Status', 'Late Mins'];

        $rows = collect();

        Attendance::query()
            ->with(['employee'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('employee_id')
            ->chunk(200, function ($attendances) use (&$rows) {
                foreach ($attendances as $attendance) {
                    $rows->push([
                        $attendance->employee?->full_name ?? 'Unknown',
                        $attendance->date->format('Y-m-d'),
                        $attendance->time_in?->format('H:i') ?? '',
                        $attendance->time_out?->format('H:i') ?? '',
                        $attendance->status?->label() ?? '',
                        (string) ($attendance->late_minutes ?? 0),
                    ]);
                }
            });

        return $this->toCsv($headers, $rows->toArray());
    }

    /**
     * Convert headers and rows to CSV string.
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, string>>  $rows
     */
    private function toCsv(array $headers, array $rows): string
    {
        $stream = fopen('php://temp', 'r+b');

        // Write BOM for Excel compatibility with UTF-8
        fwrite($stream, "\xEF\xBB\xBF");

        // Write headers
        fputcsv($stream, $headers);

        // Write rows
        foreach ($rows as $row) {
            fputcsv($stream, $row);
        }

        rewind($stream);

        $csv = stream_get_contents($stream);
        fclose($stream);

        return $csv !== false ? $csv : '';
    }
}
