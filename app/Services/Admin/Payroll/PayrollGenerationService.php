<?php

namespace App\Services\Admin\Payroll;

use App\Enums\Status\AttendanceStatus;
use App\Enums\Status\RequestStatus;
use App\Enums\Type\PayrollAdjustmentType;
use App\Enums\Type\RequestType;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollAdjustment;
use App\Models\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayrollGenerationService
{
    /**
     * Configuration for payroll calculation.
     * These can be moved to config file or database settings.
     */
    protected array $config = [
        'working_days_per_month' => 22,
        'hourly_rate_multiplier' => 1.25, // Overtime rate multiplier
        'late_deduction_per_minute' => 0.50,
    ];

    /**
     * Generate payroll for an employee for a given period.
     */
    public function generate(Employee $employee, \DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): Payroll
    {
        return DB::transaction(function () use ($employee, $periodStart, $periodEnd) {
            // Get attendances for the period
            $attendances = $this->getAttendancesForPeriod($employee, $periodStart, $periodEnd);

            // Calculate working days (excluding approved leaves)
            $workingDays = $this->calculateWorkingDays($attendances, $periodStart, $periodEnd);

            // Get basic salary from employee or use default
            $basicSalary = $this->getBasicSalary($employee);

            // Calculate daily rate
            $dailyRate = $basicSalary / $this->config['working_days_per_month'];

            // Calculate earned salary based on working days
            $earnedSalary = $dailyRate * $workingDays['present_days'];

            // Calculate overtime pay
            $overtimePay = $this->calculateOvertimePay($attendances, $dailyRate);

            // Calculate late deduction
            $lateDeduction = $this->calculateLateDeduction($attendances);

            // Calculate gross pay
            $grossPay = $earnedSalary + $overtimePay - $lateDeduction;

            // Calculate deductions (tax, SSS)
            $tax = $this->calculateTax($grossPay);
            $sss = $this->calculateSSS($basicSalary);

            // Calculate allowances (could be from employee profile or settings)
            $allowance = $this->getAllowance($employee);

            // Calculate net pay
            $netPay = $grossPay - $tax - $sss;

            // Create payroll record
            $payroll = Payroll::create([
                'employee_id' => $employee->id,
                'basic_salary' => $basicSalary,
                'tax' => $tax,
                'sss' => $sss,
                'allowance' => $allowance,
                'net_pay' => $netPay,
                'pay_period_start' => $periodStart,
                'pay_period_end' => $periodEnd,
            ]);

            // Create adjustment records for tracking
            $this->createAdjustments($payroll, [
                'overtime_pay' => $overtimePay,
                'late_deduction' => $lateDeduction,
                'allowance' => $allowance,
            ]);

            return $payroll;
        });
    }

    /**
     * Generate payroll for all employees for a given period.
     */
    public function generateForAllEmployees(\DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): Collection
    {
        $employees = Employee::where('status', \App\Enums\Status\EmployeeStatus::ACTIVE)->get();

        return $employees->map(function ($employee) use ($periodStart, $periodEnd) {
            return $this->generate($employee, $periodStart, $periodEnd);
        });
    }

    /**
     * Get attendances for the period.
     */
    protected function getAttendancesForPeriod(Employee $employee, \DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): Collection
    {
        return Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->get();
    }

    /**
     * Calculate working days excluding approved leaves.
     */
    protected function calculateWorkingDays(Collection $attendances, \DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): array
    {
        $totalDays = $periodEnd->diff($periodStart)->days + 1;

        // Get approved leave requests for this period
        $approvedLeaves = Request::where('employee_id', $attendances->first()?->employee_id)
            ->where('type', RequestType::LEAVE)
            ->where('status', RequestStatus::APPROVED)
            ->whereBetween('requested_date', [$periodStart, $periodEnd])
            ->get();

        // Calculate leave days
        $leaveDays = 0;
        foreach ($approvedLeaves as $leave) {
            $endDate = $leave->end_date ?? $leave->requested_date;
            $leaveDays += $leave->requested_date->diff($endDate)->days + 1;
        }

        // Present days = total days - leave days
        $presentDays = $totalDays - $leaveDays;

        return [
            'total_days' => $totalDays,
            'leave_days' => $leaveDays,
            'present_days' => max(0, $presentDays),
        ];
    }

    /**
     * Get basic salary for the employee.
     * This could be from employee profile or a salary configuration.
     */
    protected function getBasicSalary(Employee $employee): float
    {
        // For now, return a default. In production, this should come from
        // employee profile or a separate salary configuration
        return $employee->position?->salary ?? 25000.00;
    }

    /**
     * Calculate overtime pay.
     */
    protected function calculateOvertimePay(Collection $attendances, float $dailyRate): float
    {
        $hourlyRate = $dailyRate / 8; // Assuming 8 hours per day
        $overtimeRate = $hourlyRate * $this->config['hourly_rate_multiplier'];

        $totalOvertimeMinutes = $attendances->sum('overtime_minutes');
        $overtimeHours = $totalOvertimeMinutes / 60;

        return $overtimeHours * $overtimeRate;
    }

    /**
     * Calculate late deduction.
     */
    protected function calculateLateDeduction(Collection $attendances): float
    {
        $totalLateMinutes = $attendances->sum('late_minutes');

        return $totalLateMinutes * $this->config['late_deduction_per_minute'];
    }

    /**
     * Calculate tax based on gross pay.
     * This is a simplified tax calculation. In production, use actual tax brackets.
     */
    protected function calculateTax(float $grossPay): float
    {
        // Simplified tax calculation (Philippines-style brackets)
        if ($grossPay <= 15000) {
            return 0;
        } elseif ($grossPay <= 30000) {
            return ($grossPay - 15000) * 0.05;
        } elseif ($grossPay <= 70000) {
            return 750 + ($grossPay - 30000) * 0.10;
        } elseif ($grossPay <= 140000) {
            return 4750 + ($grossPay - 70000) * 0.15;
        } elseif ($grossPay <= 250000) {
            return 15250 + ($grossPay - 140000) * 0.20;
        } elseif ($grossPay <= 500000) {
            return 37250 + ($grossPay - 250000) * 0.25;
        } else {
            return 99750 + ($grossPay - 500000) * 0.30;
        }
    }

    /**
     * Calculate SSS contribution.
     * This is a simplified calculation. Use actual SSS contribution table.
     */
    protected function calculateSSS(float $basicSalary): float
    {
        // Simplified SSS contribution calculation
        if ($basicSalary < 5000) {
            return 200;
        } elseif ($basicSalary < 7500) {
            return 350;
        } elseif ($basicSalary < 10000) {
            return 500;
        } elseif ($basicSalary < 12500) {
            return 650;
        } elseif ($basicSalary < 15000) {
            return 800;
        } elseif ($basicSalary < 17500) {
            return 950;
        } elseif ($basicSalary < 20000) {
            return 1100;
        } elseif ($basicSalary < 25000) {
            return 1250;
        } else {
            return 1350;
        }
    }

    /**
     * Get allowance for the employee.
     */
    protected function getAllowance(Employee $employee): float
    {
        // This could come from employee profile or settings
        return $employee->position?->allowance ?? 0;
    }

    /**
     * Create adjustment records for tracking.
     */
    protected function createAdjustments(Payroll $payroll, array $details): void
    {
        // Create overtime adjustment if any
        if ($details['overtime_pay'] > 0) {
            PayrollAdjustment::create([
                'payroll_id' => $payroll->id,
                'type' => PayrollAdjustmentType::BONUS,
                'reason' => 'Overtime Pay',
                'amount' => $details['overtime_pay'],
            ]);
        }

        // Create late deduction adjustment if any
        if ($details['late_deduction'] > 0) {
            PayrollAdjustment::create([
                'payroll_id' => $payroll->id,
                'type' => PayrollAdjustmentType::DEDUCTION,
                'reason' => 'Late Deduction',
                'amount' => $details['late_deduction'],
            ]);
        }

        // Create allowance adjustment if any
        if ($details['allowance'] > 0) {
            PayrollAdjustment::create([
                'payroll_id' => $payroll->id,
                'type' => PayrollAdjustmentType::BONUS,
                'reason' => 'Allowance',
                'amount' => $details['allowance'],
            ]);
        }
    }
}
