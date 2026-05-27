<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Admin\Employee;

use App\Enums\Type\EmployeeType;
use App\Models\Employee;
use App\Models\Position;
use App\Services\Admin\Employee\EmployeeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EmployeeServiceTest extends TestCase
{
    use RefreshDatabase;

    private EmployeeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(EmployeeService::class);
    }

    public function test_store_creates_employee_from_request(): void
    {
        $position = Position::factory()->create();

        $payload = [
            'position_id' => $position->id,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'birthdate' => '1995-06-15',
            'mobile_number' => '09171234567',
            'email' => 'maria.santos@example.com',
            'type' => EmployeeType::REGULAR->value,
        ];

        $request = $this->mock(FormRequest::class);
        $request->shouldReceive('validated')->andReturnUsing(
            fn (?string $key = null, mixed $default = null) => $key !== null ? ($payload[$key] ?? $default) : $payload
        );

        $employee = $this->service->store(Employee::class, $request);

        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertSame('Maria', $employee->first_name);
        $this->assertSame('Santos', $employee->last_name);
        $this->assertSame($position->id, $employee->position_id);
        $this->assertDatabaseHas('employees', [
            'email' => 'maria.santos@example.com',
        ]);
    }

    public function test_store_sets_defaults_for_optional_fields(): void
    {
        $position = Position::factory()->create();

        $payload = [
            'position_id' => $position->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'type' => EmployeeType::REGULAR->value,
            'mobile_number' => '09170000000',
        ];

        $request = $this->mock(FormRequest::class);
        $request->shouldReceive('validated')->andReturnUsing(
            fn (?string $key = null, mixed $default = null) => $key !== null ? ($payload[$key] ?? $default) : $payload
        );

        $employee = $this->service->store(Employee::class, $request);

        $this->assertNull($employee->middle_name);
        $this->assertNull($employee->birthdate);
    }

    public function test_update_modifies_employee_attributes(): void
    {
        $position = Position::factory()->create();
        $employee = Employee::factory()->create();

        $payload = [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'position_id' => $position->id,
            'email' => 'updated.name@example.com',
            'type' => EmployeeType::REGULAR->value,
            'mobile_number' => '09170000001',
        ];

        $request = $this->mock(FormRequest::class);
        $request->shouldReceive('validated')->andReturnUsing(
            fn (?string $key = null, mixed $default = null) => $key !== null ? ($payload[$key] ?? $default) : $payload
        );

        $updated = $this->service->update($employee, $request);

        $this->assertSame('Updated', $updated->first_name);
        $this->assertSame('Name', $updated->last_name);
    }
}
