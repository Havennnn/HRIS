<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DepartmentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_department(): void
    {
        $department = Department::factory()->create([
            'name' => 'Human Resources',
        ]);

        $this->assertModelExists($department);
        $this->assertSame('Human Resources', $department->name);
    }

    public function test_department_has_positions(): void
    {
        $department = Department::factory()->create();
        $position = Position::factory()->create([
            'department_id' => $department->id,
        ]);

        $this->assertTrue($department->positions->contains($position));
    }

    public function test_department_has_activity_logs(): void
    {
        $department = Department::factory()->create();

        $this->assertContains(
            'PiaCore\Models\Concerns\HasActivityLogs',
            class_uses_recursive(Department::class)
        );
    }

    public function test_department_has_archives(): void
    {
        $department = Department::factory()->create();

        $this->assertContains(
            'PiaCore\Models\Concerns\HasArchives',
            class_uses_recursive(Department::class)
        );
    }
}
