<?php

namespace App\Http\Resources\Admin\Payroll;

use App\Enums\Status\AttendanceStatus;
use App\Enums\Status\RequestStatus;
use App\Enums\Type\RequestType;
use App\Models\Attendance;
use App\Models\Holiday;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Payroll
 */
class PayrollShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $periodStart = CarbonImmutable::parse($this->pay_period_start)->startOfDay();
        $periodEnd = CarbonImmutable::parse($this->pay_period_end)->startOfDay();

        $attendances = Attendance::query()
            ->with('request:id,type,status')
            ->where('employee_id', $this->employee_id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->orderBy('date')
            ->get();

        // Get holidays in the period
        $holidays = Holiday::query()
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->get()
            ->keyBy(fn ($holiday) => CarbonImmutable::parse($holiday->date)->toDateString());

        // Get all attendances with holiday info (for display)
        $allAttendancesWithHoliday = $attendances->map(function (Attendance $attendance) use ($holidays) {
            $date = CarbonImmutable::parse($attendance->date)->toDateString();
            $holiday = $holidays->get($date);

            return [
                'id' => $attendance->id,
                'date' => $attendance->date?->format('M d, Y'),
                'time_in' => $attendance->time_in?->format('H:i'),
                'time_out' => $attendance->time_out?->format('H:i'),
                'late_minutes' => (int) ($attendance->late_minutes ?? 0),
                'overtime_minutes' => (int) ($attendance->overtime_minutes ?? 0),
                'ot_approved' => $attendance->request?->type === RequestType::OVERTIME
                    && $attendance->request?->status === RequestStatus::APPROVED,
                'ot_approved_label' => $attendance->request?->type === RequestType::OVERTIME
                    && $attendance->request?->status === RequestStatus::APPROVED ? 'Yes' : 'No',
                'is_holiday' => $holiday !== null,
                'holiday_name' => $holiday?->name,
                'holiday_type' => $holiday?->type?->label(),
                'holiday_type_value' => $holiday?->type?->value,
                'has_work_on_holiday_approved' => $holiday !== null && 
                    $attendance->request?->type === RequestType::WORK_ON_HOLIDAY &&
                    $attendance->request?->status === RequestStatus::APPROVED,
            ];
        });

        // For payable attendances (calculation): exclude holidays without approved WORK_ON_HOLIDAY request
        $payableAttendances = $attendances
            ->filter(function (Attendance $attendance) use ($holidays) {
                // Skip absent
                if ($attendance->status === AttendanceStatus::ABSENT) {
                    return false;
                }

                // Skip attendance on holidays without approved WORK_ON_HOLIDAY request
                $date = CarbonImmutable::parse($attendance->date)->toDateString();
                $holiday = $holidays->get($date);

                if ($holiday !== null) {
                    $hasApprovedWorkOnHoliday = $attendance->request?->type === RequestType::WORK_ON_HOLIDAY
                        && $attendance->request?->status === RequestStatus::APPROVED;

                    if (!$hasApprovedWorkOnHoliday) {
                        return false;
                    }
                }

                return true;
            })
            ->values();

        $recordedDays = $payableAttendances
            ->unique(fn (Attendance $attendance) => CarbonImmutable::parse($attendance->date)->toDateString())
            ->count();

        // Calculate expected weekdays and subtract weekday holidays
        $expectedWeekdays = 0;
        $cursor = $periodStart;

        while ($cursor->lessThanOrEqualTo($periodEnd)) {
            if (! $cursor->isWeekend()) {
                // Check if this day is a holiday
                $dateStr = $cursor->toDateString();
                $isHoliday = $holidays->has($dateStr);
                
                if (!$isHoliday) {
                    $expectedWeekdays++;
                }
            }

            $cursor = $cursor->addDay();
        }

        $approvedOvertimeMinutes = (int) $payableAttendances
            ->filter(fn (Attendance $attendance) =>
                $attendance->request?->type === RequestType::OVERTIME
                && $attendance->request?->status === RequestStatus::APPROVED
            )
            ->sum('overtime_minutes');

        $lateMinutes = (int) $payableAttendances->sum('late_minutes');

        $basicSalary = (float) $this->basic_salary;
        $dailyRate = $basicSalary / max(1, (float) config('payroll.working_days_per_month', 22));
        $hourlyRate = $dailyRate / max(1, (float) config('payroll.work_hours_per_day', 8));
        $overtimeRate = $hourlyRate * (float) config('payroll.hourly_rate_multiplier', 1.25);
        $overtimePay = round(($approvedOvertimeMinutes / 60) * $overtimeRate, 2);
        $lateDeduction = round($lateMinutes * (float) config('payroll.late_deduction_per_minute', 0.5), 2);

        return [
            'id' => $this->id,
            'employee' => [
                'id' => $this->employee?->id,
                'full_name' => $this->employee?->full_name,
                'email' => $this->employee?->email,
                'position' => $this->employee?->position?->name,
                'position_level' => $this->employee?->position?->level,
                'department' => $this->employee?->position?->department?->name,
            ],
            'status_value' => $this->status?->value,
            'status' => $this->status?->badge(),
            'basic_salary' => (float) $this->basic_salary,
            'allowance' => (float) $this->allowance,
            'tax' => (float) $this->tax,
            'sss' => (float) $this->sss,
            'pagibig' => (float) $this->pagibig,
            'philhealth' => (float) $this->philhealth,
            'gross_pay' => (float) $this->gross_pay,
            'net_pay' => (float) $this->net_pay,
            'payment_breakdown' => [
                'overtime_minutes' => $approvedOvertimeMinutes,
                'overtime_pay' => (float) $overtimePay,
                'late_minutes' => $lateMinutes,
                'late_deduction' => (float) $lateDeduction,
            ],
            'pay_period_start' => $this->pay_period_start?->format('M d, Y'),
            'pay_period_end' => $this->pay_period_end?->format('M d, Y'),
            'adjustments' => $this->adjustments->map(fn ($adjustment) => [
                'id' => $adjustment->id,
                'type' => $adjustment->type?->label(),
                'type_value' => $adjustment->type?->value,
                'reason' => $adjustment->reason,
                'amount' => (float) $adjustment->amount,
            ])->values(),
            'attendance_summary' => [
                'recorded_days' => $recordedDays,
                'expected_days' => $expectedWeekdays,
                'ratio' => $expectedWeekdays > 0 ? "{$recordedDays}/{$expectedWeekdays}" : '0/0',
            ],
            'attendance_logs' => $allAttendancesWithHoliday,
            'created_at' => $this->created_at?->format('M d, Y H:i:s'),
            'updated_at' => $this->updated_at?->format('M d, Y H:i:s'),
        ];
    }
}
