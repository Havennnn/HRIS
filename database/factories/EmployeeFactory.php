<?php

namespace Database\Factories;

use App\Enums\Status\EmployeeStatus;
use App\Enums\Type\EmployeeType;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        
        return [
            'position_id' => Position::factory(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $this->faker->optional()->firstName(),
            'birthdate' => $this->faker->date('Y-m-d', '-18 years'),
            'hired_date' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'mobile_number' => $this->faker->phoneNumber(),
            'email' => strtolower($firstName . '.' . $lastName . '@' . $this->faker->domainName()),
            'status' => EmployeeStatus::ACTIVE,
            'type' => EmployeeType::REGULAR,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Employee $employee) {
            if (empty($employee->email)) {
                $employee->email = strtolower($employee->first_name . '.' . $employee->last_name . '@example.com');
            }
        });
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EmployeeStatus::INACTIVE,
        ]);
    }

    public function trainee(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => EmployeeType::TRAINEE,
        ]);
    }

    public function probationary(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => EmployeeType::PROBATIONARY,
        ]);
    }
}
