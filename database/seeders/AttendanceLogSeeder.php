<?php

namespace Database\Seeders;

use App\Enums\Type\AttendanceLogType;
use App\Models\AttendanceLog;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class AttendanceLogSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            // Create 10 attendance log records per employee
            for ($i = 0; $i < 10; $i++) {
                $type = rand(1, 100) <= 50 ? AttendanceLogType::IN : AttendanceLogType::OUT;
                $timestamp = now()->subDays(rand(1, 30));

                // Adjust timestamp based on type
                if ($type === AttendanceLogType::IN) {
                    // Clock in between 7 AM and 10 AM
                    $timestamp->setTime(rand(7, 10), rand(0, 59), rand(0, 59));
                } else {
                    // Clock out between 4 PM and 9 PM
                    $timestamp->setTime(rand(16, 21), rand(0, 59), rand(0, 59));
                }

                AttendanceLog::query()->create([
                    'employee_id' => $employee->id,
                    'type' => $type,
                    'timestamp' => $timestamp,
                ]);
            }
        }
    }
}
