<?php

namespace App\Services\Api\V1\Attendance;

use App\Enums\Status\AttendanceStatus;
use App\Enums\Type\AttendanceLogType;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\AttendanceTag;
use App\Models\Employee;
use App\Models\Request as RequestModel;
use App\Models\ShiftSchedule;
use App\Enums\Status\RequestStatus;
use App\Enums\Type\RequestType;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceApiService
{
    /**
     * Record employee time in for today.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function timeIn(?Employee $employee): array
    {
        if (! $employee instanceof Employee) {
            throw new Exception('Unauthorized.');
        }

        try {
            return DB::transaction(function () use ($employee): array {
                $now = Carbon::now();
                $today = $now->toDateString();

                /** @var Attendance|null $attendance */
                $attendance = Attendance::query()
                    ->with('tags')
                    ->where('employee_id', $employee->id)
                    ->whereDate('date', $today)
                    ->first();

                if ($attendance?->time_in !== null) {
                    throw new Exception('You already have a time in record for today.');
                }

                $scheduledStart = $this->resolveScheduledStartTime($employee, $now);
                $lateMinutes = 0;
                $status = AttendanceStatus::PRESENT;

                if ($scheduledStart !== null && $now->greaterThan($scheduledStart)) {
                    $lateMinutes = $scheduledStart->diffInMinutes($now);
                    $status = $lateMinutes > 0 ? AttendanceStatus::LATE : AttendanceStatus::PRESENT;
                }

                $approvedOvertimeRequest = $this->resolveApprovedOvertimeRequest($employee, $today);

                if ($attendance === null) {
                    $attendance = Attendance::query()->create([
                        'employee_id' => $employee->id,
                        'date' => $today,
                        'time_in' => $now->format('H:i:s'),
                        'time_out' => null,
                        'late_minutes' => $lateMinutes,
                        'overtime_minutes' => 0,
                        'status' => $status->value,
                        'request_id' => $approvedOvertimeRequest?->id,
                    ]);
                } else {
                    $attendance->update([
                        'time_in' => $now->format('H:i:s'),
                        'late_minutes' => $lateMinutes,
                        'status' => $status->value,
                        'request_id' => $approvedOvertimeRequest?->id,
                    ]);
                }

                $this->syncAttendanceStatusTag($attendance, $status);

                $log = AttendanceLog::query()->create([
                    'employee_id' => $employee->id,
                    'type' => AttendanceLogType::IN,
                    'timestamp' => $now,
                ]);

                return $this->formatAttendanceSummary($attendance->fresh('tags'), $log);
            });
        } catch (Exception $e) {
            throw new Exception('Failed to record time in: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Record employee time out for today.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function timeOut(?Employee $employee): array
    {
        if (! $employee instanceof Employee) {
            throw new Exception('Unauthorized.');
        }

        try {
            return DB::transaction(function () use ($employee): array {
                $now = Carbon::now();
                $today = $now->toDateString();

                /** @var Attendance|null $attendance */
                $attendance = Attendance::query()
                    ->with('tags')
                    ->where('employee_id', $employee->id)
                    ->whereDate('date', $today)
                    ->first();

                if ($attendance === null || $attendance->time_in === null) {
                    throw new Exception('No active time in record found for today.');
                }

                if ($attendance->time_out !== null) {
                    throw new Exception('You already have a time out record for today.');
                }

                $scheduledEnd = $this->resolveScheduledEndTime($employee, $now);
                $overtimeMinutes = 0;

                if ($scheduledEnd !== null && $now->greaterThan($scheduledEnd)) {
                    $overtimeMinutes = $scheduledEnd->diffInMinutes($now);
                }

                $approvedOvertimeRequest = $this->resolveApprovedOvertimeRequest($employee, $today);
                $isLate = (int) $attendance->late_minutes > 0;
                $isEarlyOut = $scheduledEnd !== null && $now->lt($scheduledEnd);
                $hasOvertime = $overtimeMinutes > 0;
                $hasApprovedOvertime = $approvedOvertimeRequest instanceof RequestModel;

                $status = $this->resolveStatusForTimeOut(
                    isLate: $isLate,
                    isEarlyOut: $isEarlyOut,
                    hasOvertime: $hasOvertime,
                    hasApprovedOvertime: $hasApprovedOvertime,
                );

                $attendance->update([
                    'time_out' => $now->format('H:i:s'),
                    'overtime_minutes' => $overtimeMinutes,
                    'status' => $status->value,
                    'request_id' => $hasApprovedOvertime ? $approvedOvertimeRequest->id : null,
                ]);

                $this->syncAttendanceStatusTag($attendance, $status);

                $log = AttendanceLog::query()->create([
                    'employee_id' => $employee->id,
                    'type' => AttendanceLogType::OUT,
                    'timestamp' => $now,
                ]);

                return $this->formatAttendanceSummary($attendance->fresh('tags'), $log);
            });
        } catch (Exception $e) {
            throw new Exception('Failed to record time out: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Get today's attendance and logs for the authenticated employee.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function getTodayAttendance(?Employee $employee): array
    {
        if (! $employee instanceof Employee) {
            throw new Exception('Unauthorized.');
        }

        try {
            $today = Carbon::now()->toDateString();

            /** @var Attendance|null $attendance */
            $attendance = Attendance::query()
                ->with('tags')
                ->where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->first();

            $logs = AttendanceLog::query()
                ->where('employee_id', $employee->id)
                ->whereDate('timestamp', $today)
                ->orderByDesc('timestamp')
                ->get();

            return [
                'attendance' => $attendance
                    ? $this->formatAttendanceRow($attendance, $employee)
                    : $this->formatAbsentAttendanceRow($today),
                'latest_log' => $logs->first() ? $this->formatLogRow($logs->first()) : null,
                'logs' => $logs->map(fn (AttendanceLog $log): array => $this->formatLogRow($log))->values(),
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve today attendance: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * List all attendance records for the authenticated employee.
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws Exception
     */
    public function listAttendances(?Employee $employee): array
    {
        if (! $employee instanceof Employee) {
            throw new Exception('Unauthorized.');
        }

        try {
            return Attendance::query()
                ->with('tags')
                ->where('employee_id', $employee->id)
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->get()
                ->map(fn (Attendance $attendance): array => $this->formatAttendanceRow($attendance, $employee))
                ->all();
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve attendance list: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Resolve scheduled start time for the given date.
     */
    private function resolveScheduledStartTime(Employee $employee, Carbon $now): ?Carbon
    {
        $date = $now->copy()->startOfDay();
        $schedule = $this->resolveScheduleForDate($employee, $date);

        if ($schedule !== null) {
            return Carbon::parse($date->toDateString().' '.$schedule->start_time->format('H:i:s'));
        }

        if ($employee->shift?->start !== null) {
            return Carbon::parse($date->toDateString().' '.$employee->shift->start->format('H:i:s'));
        }

        return $this->resolveConfiguredDefaultShiftTime($date, 'start');
    }

    /**
     * Resolve scheduled end time for the given date.
     */
    private function resolveScheduledEndTime(Employee $employee, Carbon $now): ?Carbon
    {
        $date = $now->copy()->startOfDay();
        $schedule = $this->resolveScheduleForDate($employee, $date);

        if ($schedule !== null) {
            return Carbon::parse($date->toDateString().' '.$schedule->end_time->format('H:i:s'));
        }

        if ($employee->shift?->end !== null) {
            return Carbon::parse($date->toDateString().' '.$employee->shift->end->format('H:i:s'));
        }

        return $this->resolveConfiguredDefaultShiftTime($date, 'end');
    }

    /**
     * Resolve active schedule for date using ISO weekday mapping (1-7).
     */
    private function resolveScheduleForDate(Employee $employee, Carbon $date): ?ShiftSchedule
    {
        if ($employee->relationLoaded('shift')) {
            $shift = $employee->shift;
        } else {
            $shift = $employee->shift()->first();
        }

        if ($shift === null) {
            return null;
        }

        $isoWeekday = (int) $date->format('N');

        return $shift->schedules()
            ->where('weekday', $isoWeekday)
            ->whereDate('effective_from', '<=', $date->toDateString())
            ->where(function ($query) use ($date): void {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $date->toDateString());
            })
            ->orderByDesc('effective_from')
            ->first();
    }

    /**
     * Resolve default shift time from config for weekdays.
     */
    private function resolveConfiguredDefaultShiftTime(Carbon $date, string $timeType): ?Carbon
    {
        if (! config('attendance.default_shift.enabled', true)) {
            return null;
        }

        $weekdays = config('attendance.default_shift.weekdays', [1, 2, 3, 4, 5]);

        if (! is_array($weekdays)) {
            $weekdays = [1, 2, 3, 4, 5];
        }

        $normalizedWeekdays = array_map(static fn ($day): int => (int) $day, $weekdays);
        $isoWeekday = (int) $date->format('N');

        if (! in_array($isoWeekday, $normalizedWeekdays, true)) {
            return null;
        }

        $fallbackTime = $timeType === 'start'
            ? '08:00:00'
            : '16:00:00';

        $time = (string) config("attendance.default_shift.{$timeType}", $fallbackTime);

        return Carbon::parse($date->toDateString().' '.$time);
    }

    /**
     * Resolve approved overtime request for employee and date.
     */
    private function resolveApprovedOvertimeRequest(Employee $employee, string $date): ?RequestModel
    {
        return RequestModel::query()
            ->where('employee_id', $employee->id)
            ->whereDate('requested_date', $date)
            ->where('type', RequestType::OVERTIME)
            ->where('status', RequestStatus::APPROVED)
            ->latest('id')
            ->first();
    }

    /**
     * Resolve status for time-out using explicit combination rules.
     *
     * - late + overtime (approved request) => overtime
     * - late + overtime (no approved request) => late
     * - late only => late
     * - present => present
     * - late + early out => early leave
     * - present + early out => early leave
     */
    private function resolveStatusForTimeOut(
        bool $isLate,
        bool $isEarlyOut,
        bool $hasOvertime,
        bool $hasApprovedOvertime,
    ): AttendanceStatus {
        if ($hasOvertime && $hasApprovedOvertime) {
            return AttendanceStatus::OVERTIME;
        }

        if ($isEarlyOut) {
            return AttendanceStatus::EARLY_LEAVE;
        }

        if ($isLate || ($hasOvertime && ! $hasApprovedOvertime)) {
            return AttendanceStatus::LATE;
        }

        return AttendanceStatus::PRESENT;
    }

    /**
     * Build response payload for attendance summary after time-in/time-out.
     *
     * @return array<string, mixed>
     */
    private function formatAttendanceSummary(Attendance $attendance, AttendanceLog $log): array
    {
        return [
            'attendance' => $this->formatAttendanceRow($attendance, $attendance->employee()->first()),
            'log' => $this->formatLogRow($log),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatAttendanceRow(Attendance $attendance, ?Employee $employee = null): array
    {
        $status = $this->resolveAttendanceStatus($attendance);
        $state = $this->determineAttendanceState($attendance, $employee);

        return [
            'id' => $attendance->id,
            'date' => $attendance->date?->toDateString(),
            'time_in' => $attendance->time_in?->format('H:i:s'),
            'time_out' => $attendance->time_out?->format('H:i:s'),
            'late_minutes' => (int) $attendance->late_minutes,
            'overtime_minutes' => (int) $attendance->overtime_minutes,
            'status' => [
                'value' => $status?->value,
                'label' => $status?->label(),
            ],
            'attendance_state' => [
                'value' => $state,
                'label' => ucfirst(str_replace('_', ' ', $state)),
            ],
        ];
    }

    /**
     * Build an explicit absent attendance payload when no row exists for the day.
     *
     * @return array<string, mixed>
     */
    private function formatAbsentAttendanceRow(string $date): array
    {
        return [
            'id' => null,
            'date' => $date,
            'time_in' => null,
            'time_out' => null,
            'late_minutes' => 0,
            'overtime_minutes' => 0,
            'status' => [
                'value' => AttendanceStatus::ABSENT->value,
                'label' => AttendanceStatus::ABSENT->label(),
            ],
            'attendance_state' => [
                'value' => 'absent',
                'label' => 'Absent',
            ],
        ];
    }

    /**
     * Derive business state: absent, present, late, early_out.
     */
    private function determineAttendanceState(Attendance $attendance, ?Employee $employee = null): string
    {
        $status = $this->resolveAttendanceStatus($attendance);

        if ($attendance->time_in === null) {
            return 'absent';
        }

        if ((int) $attendance->overtime_minutes > 0 || $status === AttendanceStatus::OVERTIME) {
            return 'overtime';
        }

        if ((int) $attendance->late_minutes > 0 || $status === AttendanceStatus::LATE) {
            return 'late';
        }

        if ($status === AttendanceStatus::EARLY_LEAVE) {
            return 'early_out';
        }

        $resolvedEmployee = $employee;

        if (! $resolvedEmployee instanceof Employee) {
            $resolvedEmployee = $attendance->employee()->first();
        }

        if ($resolvedEmployee instanceof Employee && $attendance->time_out !== null && $attendance->date !== null) {
            $date = Carbon::parse($attendance->date->toDateString());
            $scheduledEnd = $this->resolveScheduledEndTime($resolvedEmployee, $date);

            if ($scheduledEnd !== null && $attendance->time_out->lt($scheduledEnd)) {
                return 'early_out';
            }
        }

        return 'present';
    }

    /**
     * Sync exactly one attendance status tag on the attendance.
     *
     * @throws Exception
     */
    private function syncAttendanceStatusTag(Attendance $attendance, AttendanceStatus $status): void
    {
        $tag = AttendanceTag::withTrashed()->firstOrNew([
            'value' => $status->value,
        ]);

        $tag->name = $status->label();
        $tag->deleted_at = null;
        $tag->save();

        $attendance->tags()->sync([$tag->id]);
        $attendance->setRelation('tags', collect([$tag]));
    }

    /**
     * Resolve attendance status enum from pivoted attendance tag.
     */
    private function resolveAttendanceStatus(Attendance $attendance): ?AttendanceStatus
    {
        $tag = $attendance->relationLoaded('tags')
            ? $attendance->tags->first()
            : $attendance->tags()->first();

        if (! $tag instanceof AttendanceTag) {
            return null;
        }

        if ($tag->value instanceof AttendanceStatus) {
            return $tag->value;
        }

        return AttendanceStatus::tryFrom((int) $tag->value);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatLogRow(AttendanceLog $log): array
    {
        return [
            'id' => $log->id,
            'type' => [
                'value' => $log->type?->value,
                'label' => $log->type?->label(),
            ],
            'timestamp' => $log->timestamp?->toIso8601String(),
        ];
    }
}
