<?php

namespace App\Services\Admin\Payroll;

use App\Enums\Status\AttendanceStatus;
use App\Enums\Status\EmployeeStatus;
use App\Enums\Status\PayrollStatus;
use App\Enums\Status\RequestStatus;
use App\Enums\Type\HolidayType;
use App\Enums\Type\PayrollAdjustmentType;
use App\Enums\Type\RequestType;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Payroll;
use App\Models\Request;
use App\Models\ShiftSchedule;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayrollGenerationService
{
    /**
     * Generate payroll for an employee for a given period.
     *
     * If payroll exists and is already approved/disbursed/rejected,
     * it will be kept as-is.
     */
    public function generate(Employee $employee, \DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): Payroll
    {
        $start = CarbonImmutable::parse($periodStart)->startOfDay();
        $end = CarbonImmutable::parse($periodEnd)->startOfDay();

        return DB::transaction(function () use ($employee, $start, $end) {
            $existing = Payroll::query()
                ->where('employee_id', $employee->id)
                ->whereDate('pay_period_start', $start)
                ->whereDate('pay_period_end', $end)
                ->first();

            if ($existing instanceof Payroll && $existing->status !== PayrollStatus::PENDING) {
                return $existing;
            }

            $attendances = $this->getAttendancesWithHolidayInfo($employee, $start, $end);
            $workingDays = $this->calculateWorkingDays($employee, $attendances, $start, $end);
            $customAdjustments = $existing instanceof Payroll
                ? $this->getAdminAdjustments($existing)
                : collect();

            $breakdown = $this->calculatePayrollBreakdown(
                employee: $employee,
                attendances: $attendances,
                recordedDays: $workingDays['present_days'],
                expectedDays: $workingDays['expected_days'],
                periodStart: $start,
                periodEnd: $end,
                customAdjustments: $customAdjustments,
            );

            $payroll = $existing ?? new Payroll();

            $payroll->fill([
                'employee_id' => $employee->id,
                'status' => PayrollStatus::PENDING,
                'basic_salary' => $breakdown['basic_salary'],
                'tax' => $breakdown['tax'],
                'sss' => $breakdown['sss'],
                'pagibig' => $breakdown['pagibig'],
                'philhealth' => $breakdown['philhealth'],
                'allowance' => $breakdown['allowance'],
                'gross_pay' => $breakdown['gross_pay'],
                'net_pay' => $breakdown['net_pay'],
                'pay_period_start' => $start,
                'pay_period_end' => $end,
            ]);

            $payroll->save();

            // Save holiday bonus adjustments
            $this->saveHolidayBonusAdjustments($payroll, $breakdown['holiday_bonus_details']);

            return $payroll->fresh();
        });
    }

    /**
     * Generate payroll for all active employees for a given period.
     */
    public function generateForAllEmployees(\DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): Collection
    {
        return Employee::where('status', EmployeeStatus::ACTIVE)
            ->with(['position:id,salary,allowance'])
            ->get()
            ->map(function (Employee $employee) use ($periodStart, $periodEnd) {
                return $this->generate($employee, $periodStart, $periodEnd);
            });
    }

    /**
     * Generate pending payrolls on the 10th.
     *
     * Period is Mar 1–15 but days 11–15 are ASSUMED present (weekdays only)
     * because actual attendance hasn't happened yet.
     * The 15th command will true-up those assumed days with real data.
     */
    public function generatePendingPayrollsForCutoff(?\DateTimeInterface $now = null): Collection
    {
        $today = CarbonImmutable::parse($now ?? CarbonImmutable::now())->startOfDay();

        if ($today->day !== 10) {
            return new Collection();
        }

        $periodStart = $today->startOfMonth();   // Mar 1
        $periodEnd   = $today->setDay(15);        // Mar 15 (full period)
        $assumedFrom = $today->setDay(11);        // Mar 11 onwards is assumed

        return $this->generateForAllEmployeesWithAssumedDays($periodStart, $periodEnd, $assumedFrom);
    }

    /**
     * On the 15th: true-up actual attendance for days 11–15,
     * recalculate APPROVED payrolls, and disburse them.
     */
    public function trueUpAndDisburse(?\DateTimeInterface $now = null): int
    {
        $today = CarbonImmutable::parse($now ?? CarbonImmutable::now())->startOfDay();

        if ($today->day !== 15) {
            return 0;
        }

        return $this->trueUpApprovedPayrollsForReleaseDate($today);
    }

    /**
     * Re-calculate using full actual Mar 1–15 attendance,
     * update APPROVED payrolls, and flip them to DISBURSED.
     */
    public function trueUpApprovedPayrollsForReleaseDate(\DateTimeInterface $releaseDate): int
    {
        $release     = CarbonImmutable::parse($releaseDate)->startOfDay();
        $periodStart = $release->startOfMonth();
        $periodEnd   = $release->setDay(15);

        $payrolls = Payroll::query()
            ->with('employee.position:id,salary,allowance')
            ->where('status', PayrollStatus::APPROVED)
            ->whereDate('pay_period_start', $periodStart)
            ->whereDate('pay_period_end', $periodEnd)
            ->get();

        $disbursed = 0;

        foreach ($payrolls as $payroll) {
            DB::transaction(function () use ($payroll, $periodStart, $periodEnd) {
                $employee = $payroll->employee;

                // Full actual attendance Mar 1–15 is now available
                $attendances = $this->getAttendancesWithHolidayInfo($employee, $periodStart, $periodEnd);
                $workingDays = $this->calculateWorkingDays($employee, $attendances, $periodStart, $periodEnd);
                $customAdjustments = $this->getAdminAdjustments($payroll);

                $breakdown = $this->calculatePayrollBreakdown(
                    employee: $employee,
                    attendances: $attendances,
                    recordedDays: $workingDays['present_days'],
                    expectedDays: $workingDays['expected_days'],
                    periodStart: $periodStart,
                    periodEnd: $periodEnd,
                    customAdjustments: $customAdjustments,
                );

                $payroll->fill([
                    'status'       => PayrollStatus::DISBURSED,
                    'basic_salary' => $breakdown['basic_salary'],
                    'tax'          => $breakdown['tax'],
                    'sss'          => $breakdown['sss'],
                    'pagibig'      => $breakdown['pagibig'],
                    'philhealth'   => $breakdown['philhealth'],
                    'allowance'    => $breakdown['allowance'],
                    'gross_pay'    => $breakdown['gross_pay'],
                    'net_pay'      => $breakdown['net_pay'],
                ]);

                $payroll->save();

                // Save holiday bonus adjustments
                $this->saveHolidayBonusAdjustments($payroll, $breakdown['holiday_bonus_details']);
            });

            $disbursed++;
        }

        return $disbursed;
    }

    /**
     * Generate payrolls for all active employees using assumed days for the future gap.
     */
    public function generateForAllEmployeesWithAssumedDays(
        \DateTimeInterface $periodStart,
        \DateTimeInterface $periodEnd,
        \DateTimeInterface $assumedFrom
    ): Collection {
        return Employee::where('status', EmployeeStatus::ACTIVE)
            ->with(['position:id,salary,allowance', 'shift.schedules'])
            ->get()
            ->map(function (Employee $employee) use ($periodStart, $periodEnd, $assumedFrom) {
                return $this->generateWithAssumedDays($employee, $periodStart, $periodEnd, $assumedFrom);
            });
    }

    /**
     * Generate payroll for a single employee with assumed days for the gap period.
     *
     * - Actual attendance:  periodStart → (assumedFrom - 1 day)  [Mar 1–10]
     * - Assumed attendance: assumedFrom → periodEnd               [Mar 11–15, weekdays only]
     */
    public function generateWithAssumedDays(
        Employee $employee,
        \DateTimeInterface $periodStart,
        \DateTimeInterface $periodEnd,
        \DateTimeInterface $assumedFrom
    ): Payroll {
        $start   = CarbonImmutable::parse($periodStart)->startOfDay();
        $end     = CarbonImmutable::parse($periodEnd)->startOfDay();
        $assumed = CarbonImmutable::parse($assumedFrom)->startOfDay();

        if (CarbonImmutable::now()->startOfDay()->greaterThanOrEqualTo($end)) {
            return $this->generate($employee, $start, $end);
        }

        return DB::transaction(function () use ($employee, $start, $end, $assumed) {
            $existing = Payroll::query()
                ->where('employee_id', $employee->id)
                ->whereDate('pay_period_start', $start)
                ->whereDate('pay_period_end', $end)
                ->first();

            if ($existing instanceof Payroll && $existing->status !== PayrollStatus::PENDING) {
                return $existing;
            }

            // Only actual data: Mar 1–10
            $actualEnd   = $assumed->subDay();
            $attendances = $this->getAttendancesWithHolidayInfo($employee, $start, $actualEnd);
            $workingDays = $this->calculateWorkingDays($employee, $attendances, $start, $actualEnd);
            $customAdjustments = $existing instanceof Payroll
                ? $this->getAdminAdjustments($existing)
                : collect();

            // Assumed days: based on active shift schedules (fallback Mon–Fri)
            $assumedDays = $this->countAssumedScheduledDays($employee, $assumed, $end);
            $recordedDays = $workingDays['present_days'] + $assumedDays;

            $breakdown = $this->calculatePayrollBreakdown(
                employee: $employee,
                attendances: $attendances,
                recordedDays: $recordedDays,
                expectedDays: $workingDays['expected_days'] + $assumedDays,
                periodStart: $start,
                periodEnd: $end,
                customAdjustments: $customAdjustments,
            );

            $payroll = $existing ?? new Payroll();

            $payroll->fill([
                'employee_id'      => $employee->id,
                'status'           => PayrollStatus::PENDING,
                'basic_salary'     => $breakdown['basic_salary'],
                'tax'              => $breakdown['tax'],
                'sss'              => $breakdown['sss'],
                'pagibig'          => $breakdown['pagibig'],
                'philhealth'       => $breakdown['philhealth'],
                'allowance'        => $breakdown['allowance'],
                'gross_pay'        => $breakdown['gross_pay'],
                'net_pay'          => $breakdown['net_pay'],
                'pay_period_start' => $start,
                'pay_period_end'   => $end,
            ]);

            $payroll->save();

            // Save holiday bonus adjustments
            $this->saveHolidayBonusAdjustments($payroll, $breakdown['holiday_bonus_details']);

            return $payroll->fresh();
        });
    }

    /**
     * Count weekdays (Mon–Fri) between two dates, inclusive.
     */
    protected function countWeekdays(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        $current = CarbonImmutable::parse($from)->startOfDay();
        $end     = CarbonImmutable::parse($to)->startOfDay();
        $count   = 0;

        while ($current->lessThanOrEqualTo($end)) {
            if (! $current->isWeekend()) {
                $count++;
            }
            $current = $current->addDay();
        }

        return $count;
    }

    /**
     * Count assumed present days based on active shift schedules.
     *
     * Falls back to weekday counting (Mon–Fri) when no schedule rules exist.
     */
    protected function countAssumedScheduledDays(Employee $employee, \DateTimeInterface $from, \DateTimeInterface $to): int
    {
        $start = CarbonImmutable::parse($from)->startOfDay();
        $end = CarbonImmutable::parse($to)->startOfDay();

        if ($start->greaterThan($end)) {
            return 0;
        }

        $employee->loadMissing('shift.schedules');

        $schedules = $employee->shift?->schedules;

        if (! $schedules instanceof Collection || $schedules->isEmpty()) {
            return $this->countWeekdays($start, $end);
        }

        $count = 0;
        $current = $start;

        while ($current->lessThanOrEqualTo($end)) {
            if ($this->hasActiveScheduleForDate($schedules, $current)) {
                $count++;
            }

            $current = $current->addDay();
        }

        return $count;
    }

    /**
     * Determine if a date is covered by at least one active schedule rule.
     */
    protected function hasActiveScheduleForDate(Collection $schedules, CarbonImmutable $date): bool
    {
        return $schedules->contains(function (ShiftSchedule $schedule) use ($date): bool {
            if ((int) $schedule->weekday !== $date->dayOfWeekIso) {
                return false;
            }

            $from = CarbonImmutable::parse($schedule->effective_from)->startOfDay();
            $to = $schedule->effective_to
                ? CarbonImmutable::parse($schedule->effective_to)->startOfDay()
                : null;

            if ($date->lessThan($from)) {
                return false;
            }

            if ($to !== null && $date->greaterThan($to)) {
                return false;
            }

            return true;
        });
    }

    // =========================================================================
    // Second Half: Mar 16–EOM  (generate on 25th, disburse on last day)
    // =========================================================================

    /**
     * Generate pending payrolls on the 25th.
     *
     * Period is Mar 16–EOM but days 26–EOM are ASSUMED present (weekdays only)
     * because actual attendance hasn't happened yet.
     * The last-day command will true-up those assumed days with real data.
     */
    public function generateSecondHalfPayrollsForCutoff(?\DateTimeInterface $now = null): Collection
    {
        $today = CarbonImmutable::parse($now ?? CarbonImmutable::now())->startOfDay();

        if ($today->day !== 25) {
            return new Collection();
        }

        $periodStart = $today->setDay(16);           // Mar 16
        $periodEnd   = $today->endOfMonth()->startOfDay(); // Mar 31
        $assumedFrom = $today->setDay(26);           // Mar 26 onwards is assumed

        return $this->generateForAllEmployeesWithAssumedDays($periodStart, $periodEnd, $assumedFrom);
    }

    /**
     * On the last day of the month: true-up actual attendance for days 26–EOM,
     * recalculate APPROVED payrolls, and disburse them.
     */
    public function trueUpAndDisburseSecondHalf(?\DateTimeInterface $now = null): int
    {
        $today = CarbonImmutable::parse($now ?? CarbonImmutable::now())->startOfDay();

        if (! $today->isLastOfMonth()) {
            return 0;
        }

        return $this->trueUpApprovedPayrollsForSecondHalf($today);
    }

    /**
     * Re-calculate using full actual Mar 16–EOM attendance,
     * update APPROVED payrolls, and flip them to DISBURSED.
     */
    public function trueUpApprovedPayrollsForSecondHalf(\DateTimeInterface $releaseDate): int
    {
        $release     = CarbonImmutable::parse($releaseDate)->startOfDay();
        $periodStart = $release->setDay(16);                  // Mar 16
        $periodEnd   = $release->endOfMonth()->startOfDay();  // Mar 31

        $payrolls = Payroll::query()
            ->with('employee.position:id,salary,allowance')
            ->where('status', PayrollStatus::APPROVED)
            ->whereDate('pay_period_start', $periodStart)
            ->whereDate('pay_period_end', $periodEnd)
            ->get();

        $disbursed = 0;

        foreach ($payrolls as $payroll) {
            DB::transaction(function () use ($payroll, $periodStart, $periodEnd) {
                $employee = $payroll->employee;

                // Full actual attendance Mar 16–EOM is now available
                $attendances = $this->getAttendancesWithHolidayInfo($employee, $periodStart, $periodEnd);
                $workingDays = $this->calculateWorkingDays($employee, $attendances, $periodStart, $periodEnd);
                $customAdjustments = $this->getAdminAdjustments($payroll);

                $breakdown = $this->calculatePayrollBreakdown(
                    employee: $employee,
                    attendances: $attendances,
                    recordedDays: $workingDays['present_days'],
                    expectedDays: $workingDays['expected_days'],
                    periodStart: $periodStart,
                    periodEnd: $periodEnd,
                    customAdjustments: $customAdjustments,
                );

                $payroll->fill([
                    'status'       => PayrollStatus::DISBURSED,
                    'basic_salary' => $breakdown['basic_salary'],
                    'tax'          => $breakdown['tax'],
                    'sss'          => $breakdown['sss'],
                    'pagibig'      => $breakdown['pagibig'],
                    'philhealth'   => $breakdown['philhealth'],
                    'allowance'    => $breakdown['allowance'],
                    'gross_pay'    => $breakdown['gross_pay'],
                    'net_pay'      => $breakdown['net_pay'],
                ]);

                $payroll->save();

                // Save holiday bonus adjustments
                $this->saveHolidayBonusAdjustments($payroll, $breakdown['holiday_bonus_details']);
            });

            $disbursed++;
        }

        return $disbursed;
    }

    // =========================================================================
    // Approve / Reject
    // =========================================================================

    /**
     * Approve a pending payroll.
     */
    public function approve(Payroll $payroll): Payroll
    {
        if ($payroll->status !== PayrollStatus::PENDING) {
            throw new \InvalidArgumentException('Only pending payrolls can be approved.');
        }

        $payroll->update(['status' => PayrollStatus::APPROVED]);

        return $payroll->fresh();
    }

    /**
     * Reject a pending payroll.
     */
    public function reject(Payroll $payroll): Payroll
    {
        if ($payroll->status !== PayrollStatus::PENDING) {
            throw new \InvalidArgumentException('Only pending payrolls can be rejected.');
        }

        $payroll->update(['status' => PayrollStatus::REJECTED]);

        return $payroll->fresh();
    }

    /**
     * Get attendances for the period.
     */
    protected function getAttendancesForPeriod(Employee $employee, \DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): Collection
    {
        return Attendance::query()
            ->with('request:id,type,status')
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->get();
    }

    /**
     * Get attendances for the period with holiday information.
     */
    protected function getAttendancesWithHolidayInfo(Employee $employee, \DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): Collection
    {
        $start = CarbonImmutable::parse($periodStart)->startOfDay();
        $end = CarbonImmutable::parse($periodEnd)->startOfDay();

        $attendances = $this->getAttendancesForPeriod($employee, $start, $end);

        // Get holidays in the period
        $holidays = $this->getHolidaysInRange($start, $end)
            ->keyBy(fn ($holiday) => CarbonImmutable::parse($holiday->date)->toDateString());

        // Add holiday info to each attendance
        return $attendances->map(function (Attendance $attendance) use ($holidays) {
            $date = CarbonImmutable::parse($attendance->date)->toDateString();
            $holiday = $holidays->get($date);

            $attendance->setAttribute('is_holiday', $holiday !== null);
            $attendance->setAttribute('holiday_name', $holiday?->name);
            $attendance->setAttribute('holiday_type', $holiday?->type?->label());
            $attendance->setAttribute('holiday_type_value', $holiday?->type?->value);

            return $attendance;
        });
    }

    /**
     * Calculate working-day summary for reporting and payroll computation.
     */
    protected function calculateWorkingDays(Employee $employee, Collection $attendances, \DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): array
    {
        $start = CarbonImmutable::parse($periodStart)->startOfDay();
        $end = CarbonImmutable::parse($periodEnd)->startOfDay();

        $totalDays = $start->diffInDays($end) + 1;

        $approvedLeaves = Request::query()
            ->where('employee_id', $employee->id)
            ->where('type', RequestType::LEAVE)
            ->where('status', RequestStatus::APPROVED)
            ->whereDate('requested_date', '<=', $end)
            ->where(function (Builder $query) use ($start) {
                $query->whereDate('end_date', '>=', $start)
                    ->orWhereNull('end_date');
            })
            ->get();

        $leaveDays = 0;

        foreach ($approvedLeaves as $leave) {
            $leaveStart = CarbonImmutable::parse($leave->requested_date)->startOfDay();
            $leaveEnd = CarbonImmutable::parse($leave->end_date ?? $leave->requested_date)->startOfDay();

            $overlapStart = $leaveStart->greaterThan($start) ? $leaveStart : $start;
            $overlapEnd = $leaveEnd->lessThan($end) ? $leaveEnd : $end;

            if ($overlapStart->lessThanOrEqualTo($overlapEnd)) {
                $leaveDays += $overlapStart->diffInDays($overlapEnd) + 1;
            }
        }

        $presentDays = $attendances
            ->filter(function (Attendance $attendance): bool {
                // Skip absent
                if ($attendance->status === AttendanceStatus::ABSENT) {
                    return false;
                }

                // Skip attendance on holidays without approved WORK_ON_HOLIDAY request
                if ($attendance->is_holiday) {
                    $hasApprovedWorkOnHoliday = $attendance->request?->type === RequestType::WORK_ON_HOLIDAY
                        && $attendance->request?->status === RequestStatus::APPROVED;

                    if (!$hasApprovedWorkOnHoliday) {
                        return false;
                    }
                }

                // Count PRESENT, LATE, or approved OVERTIME
                if (in_array($attendance->status, [AttendanceStatus::PRESENT, AttendanceStatus::LATE], true)) {
                    return true;
                }

                return $attendance->request?->type === RequestType::OVERTIME
                    && $attendance->request?->status === RequestStatus::APPROVED;
            })
            ->unique(fn (Attendance $attendance) => CarbonImmutable::parse($attendance->date)->toDateString())
            ->count();

        $expectedDays = $this->countAssumedScheduledDays($employee, $start, $end);

        // Exclude holidays from expected days if configured
        $holidayConfig = config('payroll.holidays', []);
        $excludeHolidays = $holidayConfig['exclude_holidays_from_expected'] ?? true;

        $holidayCount = 0;
        if ($excludeHolidays) {
            $holidayCount = $this->countHolidaysInRange($start, $end);
            $expectedDays = max(0, $expectedDays - $holidayCount);
        }

        return [
            'total_days' => $totalDays,
            'leave_days' => max(0, $leaveDays),
            'present_days' => max(0, $presentDays),
            'expected_days' => max(0, $expectedDays),
            'holiday_count' => $holidayCount,
        ];
    }

    /**
     * Count holidays within a date range (only non-archived, only weekdays).
     */
    protected function countHolidaysInRange(CarbonImmutable $start, CarbonImmutable $end): int
    {
        return Holiday::query()
            ->whereBetween('date', [$start, $end])
            ->get()
            ->filter(fn ($holiday) => !CarbonImmutable::parse($holiday->date)->isWeekend())
            ->count();
    }

    /**
     * Get holidays within a date range grouped by type (only non-archived).
     */
    protected function getHolidaysInRange(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        return Holiday::query()
            ->whereBetween('date', [$start, $end])
            ->get();
    }

    /**
     * Get approved work-on-holiday requests within a date range.
     */
    protected function getWorkOnHolidayRequests(Employee $employee, CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        return Request::query()
            ->where('employee_id', $employee->id)
            ->where('type', RequestType::WORK_ON_HOLIDAY)
            ->where('status', RequestStatus::APPROVED)
            ->whereDate('requested_date', '>=', $start)
            ->whereDate('requested_date', '<=', $end)
            ->get();
    }

    /**
     * Compute attendance-based payroll breakdown.
     */
    protected function calculatePayrollBreakdown(
        Employee $employee,
        Collection $attendances,
        int $recordedDays,
        int $expectedDays,
        \DateTimeInterface $periodStart,
        \DateTimeInterface $periodEnd,
        Collection $customAdjustments
    ): array {
        $basicSalary = $this->getBasicSalary($employee);
        $allowance = $this->getAllowance($employee, $periodEnd);
        $dailyRate = $basicSalary / max(1, (float) config('payroll.working_days_per_month'));
        $periodSalary = $basicSalary / max(1, (int) config('payroll.pay_periods_per_month', 2));

        $attendanceRatio = $expectedDays > 0 ? min(1, $recordedDays / $expectedDays) : 0;
        $attendanceGross = $this->roundMoney($periodSalary * $attendanceRatio);
        $attendanceProration = max(0, $this->roundMoney($periodSalary - $attendanceGross));

        $overtimePay = $this->calculateOvertimePay($attendances, $dailyRate);
        $lateDeduction = $this->calculateLateDeduction($attendances);

        // Calculate work-on-holiday bonus
        $holidayBonus = $this->calculateWorkOnHolidayBonus($employee, $attendances, $dailyRate, $periodStart, $periodEnd);

        $customBonus = $this->roundMoney(
            (float) $customAdjustments
                ->where('type', PayrollAdjustmentType::BONUS)
                ->sum('amount')
        );

        $customDeduction = $this->roundMoney(
            (float) $customAdjustments
                ->where('type', PayrollAdjustmentType::DEDUCTION)
                ->sum('amount')
        );

        $grossPay = $this->roundMoney($attendanceGross + $allowance + $overtimePay + $holidayBonus['total'] + $customBonus);
        $taxableGrossPay = $this->roundMoney(max(0, $grossPay - $lateDeduction - $customDeduction));

        $sss = $this->calculateSSS($basicSalary);
        $pagibig = $this->calculatePagibig($basicSalary);
        $philhealth = $this->calculatePhilHealth($basicSalary);
        $taxableCompensation = max(0, $taxableGrossPay - $sss - $pagibig - $philhealth);
        $tax = $this->calculateTax($taxableCompensation);

        $totalDeductions = $this->roundMoney(
            $tax
            + $sss
            + $pagibig
            + $philhealth
            + $lateDeduction
            + $customDeduction
        );

        $netPay = $this->roundMoney($grossPay - $totalDeductions);

        return [
            'basic_salary' => $this->roundMoney($basicSalary),
            'allowance' => $this->roundMoney($allowance),
            'attendance_proration' => $attendanceProration,
            'overtime_pay' => $overtimePay,
            'holiday_bonus' => $holidayBonus['total'],
            'holiday_bonus_details' => $holidayBonus['details'],
            'late_deduction' => $lateDeduction,
            'gross_pay' => max(0, $grossPay),
            'tax' => $tax,
            'sss' => $sss,
            'pagibig' => $pagibig,
            'philhealth' => $philhealth,
            'net_pay' => max(0, $netPay),
        ];
    }

    /**
     * Calculate bonus for work-on-holiday requests.
     */
    protected function calculateWorkOnHolidayBonus(
        Employee $employee,
        Collection $attendances,
        float $dailyRate,
        \DateTimeInterface $periodStart,
        \DateTimeInterface $periodEnd
    ): array {
        $start = CarbonImmutable::parse($periodStart)->startOfDay();
        $end = CarbonImmutable::parse($periodEnd)->startOfDay();

        $holidayConfig = config('payroll.holidays', []);
        $regularMultiplier = (float) ($holidayConfig['regular_holiday_multiplier'] ?? 1.0);
        $specialMultiplier = (float) ($holidayConfig['special_holiday_multiplier'] ?? 0.5);

        // Get approved work-on-holiday requests
        $workOnHolidayRequests = $this->getWorkOnHolidayRequests($employee, $start, $end);

        $totalBonus = 0.0;
        $holidayBonusDetails = [];

        foreach ($workOnHolidayRequests as $request) {
            $requestDate = CarbonImmutable::parse($request->requested_date)->startOfDay();

            // Get the holiday for this date
            $holiday = Holiday::query()
                ->whereDate('date', $requestDate)
                ->first();

            if ($holiday) {
                $multiplier = $holiday->type === HolidayType::REGULAR
                    ? $regularMultiplier
                    : $specialMultiplier;

                $bonusAmount = $this->roundMoney($dailyRate * $multiplier);
                $totalBonus += $bonusAmount;

                $holidayBonusDetails[] = [
                    'date' => $requestDate->format('Y-m-d'),
                    'holiday_name' => $holiday->name,
                    'holiday_type' => $holiday->type->label(),
                    'multiplier' => $multiplier,
                    'bonus_amount' => $bonusAmount,
                    'reason' => "Work on {$holiday->type->label()} Holiday '{$holiday->name}' ({$multiplier}x daily rate)",
                ];
            }
        }

        return [
            'total' => $this->roundMoney($totalBonus),
            'details' => $holidayBonusDetails,
        ];
    }

    /**
     * Get non-system adjustments to preserve and include in computation.
     */
    protected function getAdminAdjustments(Payroll $payroll): Collection
    {
        return $payroll->adjustments()
            ->get(['type', 'reason', 'amount']);
    }

    /**
     * Get basic salary for the employee.
     */
    protected function getBasicSalary(Employee $employee): float
    {
        return (float) ($employee->position?->salary ?? 25000.00);
    }

    /**
     * Calculate overtime pay.
     */
    protected function calculateOvertimePay(Collection $attendances, float $dailyRate): float
    {
        $workHoursPerDay = max(1, (float) config('payroll.work_hours_per_day', 8));
        $hourlyRate = $dailyRate / $workHoursPerDay;
        $overtimeRate = $hourlyRate * config('payroll.hourly_rate_multiplier');

        $totalOvertimeMinutes = (int) $attendances
            ->filter(fn (Attendance $attendance): bool =>
                $attendance->request?->type === RequestType::OVERTIME
                && $attendance->request?->status === RequestStatus::APPROVED
            )
            ->sum('overtime_minutes');
        $overtimeHours = $totalOvertimeMinutes / 60;

        return $this->roundMoney($overtimeHours * $overtimeRate);
    }

    /**
     * Calculate late deduction.
     */
    protected function calculateLateDeduction(Collection $attendances): float
    {
        $totalLateMinutes = (int) $attendances->sum('late_minutes');

        return $this->roundMoney($totalLateMinutes * config('payroll.late_deduction_per_minute'));
    }

    /**
     * Calculate withholding tax using PH monthly brackets,
     * normalized back to the current payroll period (e.g. semi-monthly).
     */
    protected function calculateTax(float $taxableCompensation): float
    {
        $payPeriodsPerMonth = max(1, (int) config('payroll.pay_periods_per_month', 2));
        $monthlyTaxable = $taxableCompensation * $payPeriodsPerMonth;

        $brackets = config('payroll.bir_withholding_monthly', []);

        foreach ($brackets as $bracket) {
            $withinUpper = $bracket['to'] === null || $monthlyTaxable < $bracket['to'];

            if ($monthlyTaxable >= $bracket['from'] && $withinUpper) {
                $monthlyTax = $bracket['base'] + (($monthlyTaxable - $bracket['from']) * $bracket['rate']);

                return $this->roundMoney(max(0, $monthlyTax / $payPeriodsPerMonth));
            }
        }

        return 0;
    }

    /**
     * Calculate SSS contribution.
     * This is a simplified calculation. Use actual SSS contribution table.
     */
    protected function calculateSSS(float $basicSalary): float
    {
        $table = config('payroll.sss_table', []);
        $divisor = $this->getContributionDivisor();

        foreach ($table as $row) {
            $withinUpper = $row['to'] === null || $basicSalary < $row['to'];

            if ($basicSalary >= $row['from'] && $withinUpper) {
                $monthlyContribution = (float) $row['contribution'];

                return $this->roundMoney($monthlyContribution / $divisor);
            }
        }

        return 0;
    }

    /**
     * Calculate Pag-IBIG employee contribution.
     */
    protected function calculatePagibig(float $basicSalary): float
    {
        $thresholdSalary = (float) config('payroll.pagibig.threshold_salary', 1500);
        $rateBelow = (float) config('payroll.pagibig.rate_below_threshold', 0.01);
        $rateAtOrAbove = (float) config('payroll.pagibig.rate_at_or_above_threshold', 0.02);
        $maxSalaryBase = (float) config('payroll.pagibig.max_salary_base', 5000);
        $divisor = $this->getContributionDivisor();

        $salaryBase = min($basicSalary, $maxSalaryBase);
        $rate = $basicSalary < $thresholdSalary ? $rateBelow : $rateAtOrAbove;

        $monthlyContribution = $salaryBase * $rate;

        return $this->roundMoney($monthlyContribution / $divisor);
    }

    /**
     * Calculate PhilHealth employee contribution.
     */
    protected function calculatePhilHealth(float $basicSalary): float
    {
        $premiumRate = (float) config('payroll.philhealth.premium_rate', 0.05);
        $employeeShare = (float) config('payroll.philhealth.employee_share', 0.50);
        $minSalaryBase = (float) config('payroll.philhealth.min_salary_base', 10000);
        $maxSalaryBase = (float) config('payroll.philhealth.max_salary_base', 100000);
        $divisor = $this->getContributionDivisor();

        $salaryBase = min(max($basicSalary, $minSalaryBase), $maxSalaryBase);

        $monthlyContribution = $salaryBase * $premiumRate * $employeeShare;

        return $this->roundMoney($monthlyContribution / $divisor);
    }

    /**
     * Determine how monthly contribution amounts are divided per payroll run.
     */
    protected function getContributionDivisor(): int
    {
        $proratePerPayroll = (bool) config('payroll.contributions.prorate_per_payroll', true);

        if (! $proratePerPayroll) {
            return 1;
        }

        return max(1, (int) config('payroll.pay_periods_per_month', 2));
    }

    /**
     * Round monetary values consistently based on payroll config precision.
     */
    protected function roundMoney(float $amount): float
    {
        $precision = max(0, (int) config('payroll.money_precision', 2));

        return round($amount, $precision);
    }

    /**
     * Save holiday bonus as payroll adjustments.
     */
    protected function saveHolidayBonusAdjustments(Payroll $payroll, array $holidayBonusDetails): void
    {
        if (empty($holidayBonusDetails)) {
            return;
        }

        // Remove existing holiday bonus adjustments
        $payroll->adjustments()
            ->where('type', PayrollAdjustmentType::BONUS)
            ->where('reason', 'like', 'Work on%Holiday%')
            ->delete();

        // Add new holiday bonus adjustments
        foreach ($holidayBonusDetails as $detail) {
            $payroll->adjustments()->create([
                'type' => PayrollAdjustmentType::BONUS,
                'reason' => $detail['reason'],
                'amount' => $detail['bonus_amount'],
            ]);
        }
    }

    /**
     * Get allowance for the employee.
     */
    protected function getAllowance(Employee $employee, ?\DateTimeInterface $periodEnd = null): float
    {
        if ($periodEnd !== null) {
            $end = CarbonImmutable::parse($periodEnd)->startOfDay();

            if (! $end->isLastOfMonth()) {
                return 0.0;
            }
        }

        return (float) ($employee->position?->allowance ?? 0);
    }

}
