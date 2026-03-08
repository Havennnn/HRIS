<?php

namespace Database\Factories;

use App\Enums\Type\AttendanceLogType;
use App\Models\AttendanceLog;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceLogFactory extends Factory
{
    protected $model = AttendanceLog::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'type' => $this->faker->randomElement([
                AttendanceLogType::IN,
                AttendanceLogType::OUT,
            ]),
            'timestamp' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function clockIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AttendanceLogType::IN,
        ]);
    }

    public function clockOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AttendanceLogType::OUT,
        ]);
    }
}
