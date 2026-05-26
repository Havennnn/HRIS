<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PositionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_position(): void
    {
        $department = Department::factory()->create();
        $position = Position::factory()->create([
            'department_id' => $department->id,
            'name' => 'Software Engineer',
        ]);

        $this->assertModelExists($position);
        $this->assertSame('Software Engineer', $position->name);
    }

    public function test_position_belongs_to_department(): void
    {
        $department = Department::factory()->create();
        $position = Position::factory()->create([
            'department_id' => $department->id,
        ]);

        $this->assertTrue($position->department->is($department));
    }

    public function test_position_has_activity_logs_trait(): void
    {
        $this->assertContains(
            'PiaCore\Models\Concerns\HasActivityLogs',
            class_uses_recursive(Position::class)
        );
    }

    public function test_position_has_archives_trait(): void
    {
        $this->assertContains(
            'PiaCore\Models\Concerns\HasArchives',
            class_uses_recursive(Position::class)
        );
    }
}
