<?php

namespace Tests\Feature\Api\V1\Employee;

use App\Enums\Status\AttendanceStatus;
use App\Enums\Type\AttendanceLogType;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AttendanceTimeTrackingTest extends TestCase
{
    use RefreshDatabase;

    private const API_KEY = 'test-api-key';

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.api_key' => self::API_KEY]);
    }

    public function test_employee_can_time_in_successfully(): void
    {
        $employee = Employee::factory()->create();

        Sanctum::actingAs($employee);

        $response = $this->withHeaders([
            'X-Api-Key' => self::API_KEY,
        ])->postJson('/api/v1/attendance/time-in');

        $response->assertOk();
        $response->assertJsonPath('success', true);

        $attendance = Attendance::query()->where('employee_id', $employee->id)->whereDate('date', now()->toDateString())->first();

        $this->assertNotNull($attendance);
        $this->assertNotNull($attendance?->time_in);

        $this->assertDatabaseHas('attendance_logs', [
            'employee_id' => $employee->id,
            'type' => AttendanceLogType::IN->value,
        ]);
    }

    public function test_duplicate_time_in_is_blocked(): void
    {
        $employee = Employee::factory()->create();

        Attendance::query()->create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'time_in' => '08:00:00',
            'late_minutes' => 0,
            'overtime_minutes' => 0,
            'status' => AttendanceStatus::PRESENT,
        ]);

        Sanctum::actingAs($employee);

        $response = $this->withHeaders([
            'X-Api-Key' => self::API_KEY,
        ])->postJson('/api/v1/attendance/time-in');

        $response->assertStatus(422);
        $response->assertJsonPath('error', 'Failed to record time in: You already have a time in record for today.');
    }

    public function test_employee_can_time_out_successfully(): void
    {
        $employee = Employee::factory()->create();

        Attendance::query()->create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'time_in' => '08:00:00',
            'late_minutes' => 0,
            'overtime_minutes' => 0,
            'status' => AttendanceStatus::PRESENT,
        ]);

        Sanctum::actingAs($employee);

        $response = $this->withHeaders([
            'X-Api-Key' => self::API_KEY,
        ])->postJson('/api/v1/attendance/time-out');

        $response->assertOk();
        $response->assertJsonPath('success', true);

        $attendance = Attendance::query()->where('employee_id', $employee->id)->whereDate('date', now()->toDateString())->first();

        $this->assertNotNull($attendance);
        $this->assertNotNull($attendance?->time_out);

        $this->assertDatabaseHas('attendance_logs', [
            'employee_id' => $employee->id,
            'type' => AttendanceLogType::OUT->value,
        ]);
    }

    public function test_time_out_without_time_in_is_blocked(): void
    {
        $employee = Employee::factory()->create();

        Sanctum::actingAs($employee);

        $response = $this->withHeaders([
            'X-Api-Key' => self::API_KEY,
        ])->postJson('/api/v1/attendance/time-out');

        $response->assertStatus(422);
        $response->assertJsonPath('error', 'Failed to record time out: No active time in record found for today.');

        $this->assertDatabaseCount('attendance_logs', 0);
    }

    public function test_employee_can_list_only_own_attendance_records(): void
    {
        $employee = Employee::factory()->create();
        $otherEmployee = Employee::factory()->create();

        Attendance::query()->create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'time_in' => '08:00:00',
            'time_out' => '17:00:00',
            'late_minutes' => 0,
            'overtime_minutes' => 0,
            'status' => AttendanceStatus::PRESENT,
        ]);

        Attendance::query()->create([
            'employee_id' => $otherEmployee->id,
            'date' => now()->toDateString(),
            'time_in' => '09:00:00',
            'time_out' => '18:00:00',
            'late_minutes' => 0,
            'overtime_minutes' => 0,
            'status' => AttendanceStatus::PRESENT,
        ]);

        Sanctum::actingAs($employee);

        $response = $this->withHeaders([
            'X-Api-Key' => self::API_KEY,
        ])->getJson('/api/v1/attendance');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.date', now()->toDateString());
        $response->assertJsonPath('data.0.time_in', '08:00:00');
    }
}
