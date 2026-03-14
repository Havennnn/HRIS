<?php

namespace Database\Factories;

use App\Enums\Status\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-30 days', 'now');
        $isAbsent = $this->faker->boolean(8);
        $isLate = ! $isAbsent && $this->faker->boolean(18);

        $lateMinutes = $isLate ? $this->faker->numberBetween(5, 40) : 0;
        $timeIn = $isAbsent
            ? null
            : (clone $date)->setTime(8, 0)->modify("+{$lateMinutes} minutes");

        $overtimeMinutes = 0;

        $timeOut = $isAbsent
            ? null
            : (clone $date)->setTime(17, $this->faker->numberBetween(0, 30))->modify("+{$overtimeMinutes} minutes");

        return [
            'employee_id' => Employee::factory(),
            'date' => $date->format('Y-m-d'),
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'late_minutes' => $lateMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'status' => $isAbsent
                ? AttendanceStatus::ABSENT
                : ($isLate ? AttendanceStatus::LATE : AttendanceStatus::PRESENT),
            'request_id' => null,
        ];
    }

    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AttendanceStatus::PRESENT,
            'late_minutes' => 0,
        ]);
    }

    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AttendanceStatus::LATE,
            'late_minutes' => $this->faker->numberBetween(1, 60),
        ]);
    }

    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AttendanceStatus::ABSENT,
            'time_in' => null,
            'time_out' => null,
            'late_minutes' => 0,
            'overtime_minutes' => 0,
            'request_id' => null,
        ]);
    }
}
