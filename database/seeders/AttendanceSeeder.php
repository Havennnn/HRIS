<?php

namespace Database\Seeders;

use App\Enums\Status\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            // Create 10 attendance records per employee
            for ($i = 0; $i < 10; $i++) {
                $date = now()->subDays(rand(1, 30))->format('Y-m-d');
                $timeIn = sprintf('%02d:%02d', rand(7, 9), rand(0, 59));
                
                // Randomly decide if there's a time_out
                $hasTimeOut = rand(1, 100) <= 70;
                $timeOut = $hasTimeOut 
                    ? sprintf('%02d:%02d', rand(17, 20), rand(0, 59))
                    : null;
                
                // Calculate late minutes if time_in is after 8:00 AM
                $lateMinutes = strcmp($timeIn, '08:00') > 0 
                    ? rand(1, 60) 
                    : 0;
                
                // Calculate overtime minutes if time_out is after 6:00 PM
                $overtimeMinutes = $hasTimeOut && strcmp($timeOut ?? '18:00', '18:00') > 0
                    ? rand(1, 120)
                    : 0;

                // Determine status
                if ($hasTimeOut && $timeOut) {
                    if ($lateMinutes > 0) {
                        $status = AttendanceStatus::LATE;
                    } elseif ($overtimeMinutes > 0) {
                        $status = AttendanceStatus::OVERTIME;
                    } else {
                        $status = AttendanceStatus::PRESENT;
                    }
                } else {
                    $status = AttendanceStatus::ABSENT;
                }

                Attendance::query()->updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $date,
                    ],
                    [
                        'employee_id' => $employee->id,
                        'date' => $date,
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                        'late_minutes' => $lateMinutes,
                        'overtime_minutes' => $overtimeMinutes,
                        'status' => $status,
                    ]
                );
            }
        }
    }
}
