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
        $date = $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d');
        $timeIn = sprintf('%02d:%02d', $this->faker->numberBetween(7, 9), $this->faker->numberBetween(0, 59));
        
        // Randomly decide if there's a time_out
        $hasTimeOut = $this->faker->boolean(70);
        $timeOut = $hasTimeOut 
            ? sprintf('%02d:%02d', $this->faker->numberBetween(17, 20), $this->faker->numberBetween(0, 59))
            : null;
        
        // Calculate late minutes if time_in is after 8:00 AM
        $lateMinutes = strcmp($timeIn, '08:00') > 0 
            ? $this->faker->numberBetween(1, 60) 
            : 0;
        
        // Calculate overtime minutes if time_out is after 6:00 PM
        $overtimeMinutes = $hasTimeOut && strcmp($timeOut ?? '18:00', '18:00') > 0
            ? $this->faker->numberBetween(1, 120)
            : 0;

        return [
            'employee_id' => Employee::factory(),
            'date' => $date,
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'late_minutes' => $lateMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'status' => $hasTimeOut ? AttendanceStatus::PRESENT : AttendanceStatus::ABSENT,
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
        ]);
    }
}
