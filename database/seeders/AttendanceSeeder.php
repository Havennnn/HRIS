<?php

namespace Database\Seeders;

use App\Enums\Status\AttendanceStatus;
use App\Enums\Status\RequestStatus;
use App\Enums\Type\RequestType;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Request;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $year = (int) now()->format('Y');
        $start = CarbonImmutable::create($year, 2, 1)->startOfDay();
        $end = CarbonImmutable::create($year, 2, 28)->startOfDay();

        $dates = [];
        $cursor = $start;

        while ($cursor->lessThanOrEqualTo($end)) {
            if (! $cursor->isWeekend()) {
                $dates[] = $cursor;
            }

            $cursor = $cursor->addDay();
        }

        foreach ($employees as $employee) {
            foreach ($dates as $date) {
                $roll = random_int(1, 100);

                $isAbsent = $roll <= 8;
                $isLate = ! $isAbsent && $roll <= 26;

                $lateMinutes = $isLate ? random_int(5, 40) : 0;
                $timeIn = $isAbsent
                    ? null
                    : $date->setTime(8, 0)->addMinutes($lateMinutes);

                $overtimeMinutes = 0;
                $requestId = null;

                if (! $isAbsent && random_int(1, 100) <= 10) {
                    $overtimeMinutes = random_int(30, 120);

                    $request = Request::query()->create([
                        'employee_id' => $employee->id,
                        'type' => RequestType::OVERTIME,
                        'status' => RequestStatus::APPROVED,
                        'message' => 'Approved overtime for payroll seed data',
                        'requested_date' => $date->toDateString(),
                        'overtime_hours' => round($overtimeMinutes / 60, 2),
                    ]);

                    $requestId = $request->id;
                }

                $timeOut = $isAbsent
                    ? null
                    : $date->setTime(17, random_int(0, 30))->addMinutes($overtimeMinutes);

                Attendance::query()->updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'employee_id' => $employee->id,
                        'date' => $date->toDateString(),
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                        'late_minutes' => $lateMinutes,
                        'overtime_minutes' => $overtimeMinutes,
                        'status' => $isAbsent
                            ? AttendanceStatus::ABSENT
                            : ($isLate ? AttendanceStatus::LATE : AttendanceStatus::PRESENT),
                        'request_id' => $requestId,
                    ]
                );
            }
        }
    }
}
