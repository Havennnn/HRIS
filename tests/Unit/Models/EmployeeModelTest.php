<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EmployeeModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_employee(): void
    {
        $employee = Employee::factory()->create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan.delacruz@example.com',
        ]);

        $this->assertModelExists($employee);
        $this->assertSame('Juan', $employee->first_name);
        $this->assertSame('Dela Cruz', $employee->last_name);
    }

    public function test_employee_belongs_to_position(): void
    {
        $position = Position::factory()->create();
        $employee = Employee::factory()->create([
            'position_id' => $position->id,
        ]);

        $this->assertTrue($employee->position->is($position));
    }

    public function test_employee_belongs_to_department_through_position(): void
    {
        $department = Department::factory()->create();
        $position = Position::factory()->create([
            'department_id' => $department->id,
        ]);
        $employee = Employee::factory()->create([
            'position_id' => $position->id,
        ]);

        $this->assertSame($department->id, $employee->position->department->id);
    }

    public function test_employee_has_activity_logs_trait(): void
    {
        $this->assertContains(
            'PiaCore\Models\Concerns\HasActivityLogs',
            class_uses_recursive(Employee::class)
        );
    }

    public function test_employee_has_archives_trait(): void
    {
        $this->assertContains(
            'PiaCore\Models\Concerns\HasArchives',
            class_uses_recursive(Employee::class)
        );
    }

    public function test_employee_has_options_trait(): void
    {
        $this->assertContains(
            'PiaCore\Concerns\HasOptions',
            class_uses_recursive(Employee::class)
        );
    }
}
