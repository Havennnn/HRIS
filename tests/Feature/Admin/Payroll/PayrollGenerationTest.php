<?php

namespace Tests\Feature\Admin\Payroll;

use App\Enums\Status\AttendanceStatus;
use App\Enums\Status\PayrollStatus;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Position;
use App\Services\Admin\Payroll\PayrollGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollGenerationTest extends TestCase
{
    use RefreshDatabase;

    private PayrollGenerationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Speed up: disable default shift to avoid extra queries
        config(['attendance.default_shift.enabled' => false]);
        config(['payroll.holidays.exclude_holidays_from_expected' => false]);

        $this->service = $this->app->make(PayrollGenerationService::class);
    }

    public function test_it_creates_pending_payroll_with_basic_salary(): void
    {
        $position = Position::factory()->create([
            'salary' => 30000,
            'allowance' => 1000,
        ]);

        $employee = Employee::factory()->create([
            'position_id' => $position->id,
        ]);

        $start = now()->startOfMonth();
        $end = now()->startOfMonth()->addDays(6);

        // Create 1 day attendance so the payroll has data
        Attendance::query()->create([
            'employee_id' => $employee->id,
            'date' => $start->toDateString(),
            'time_in' => '08:00:00',
            'time_out' => '17:00:00',
            'late_minutes' => 0,
            'overtime_minutes' => 0,
            'status' => AttendanceStatus::PRESENT,
        ]);

        $payroll = $this->service->generate($employee, $start, $end);

        $this->assertNotNull($payroll);
        $this->assertEquals(30000, (int) $payroll->basic_salary);
        $this->assertEquals(PayrollStatus::PENDING->value, $payroll->status->value);
        $this->assertTrue($payroll->gross_pay > 0, 'Gross pay should be positive');
        $this->assertTrue($payroll->net_pay > 0, 'Net pay should be positive');
        $this->assertTrue($payroll->net_pay < $payroll->gross_pay, 'Net should be less than gross after deductions');
    }

    public function test_it_includes_government_contributions(): void
    {
        $position = Position::factory()->create(['salary' => 50000]);
        $employee = Employee::factory()->create(['position_id' => $position->id]);

        $start = now()->startOfMonth();
        $end = now()->startOfMonth()->addDays(6);

        Attendance::query()->create([
            'employee_id' => $employee->id,
            'date' => $start->toDateString(),
            'time_in' => '08:00:00',
            'time_out' => '17:00:00',
            'late_minutes' => 0,
            'overtime_minutes' => 0,
            'status' => AttendanceStatus::PRESENT,
        ]);

        $payroll = $this->service->generate($employee, $start, $end);

        $this->assertNotNull($payroll);
        $this->assertTrue($payroll->sss > 0, 'SSS should be present');
        $this->assertTrue($payroll->pagibig > 0, 'Pag-IBIG should be present');
        $this->assertTrue($payroll->philhealth > 0, 'PhilHealth should be present');
    }

    public function test_it_does_not_regenerate_approved_payroll(): void
    {
        $employee = Employee::factory()->create();
        $start = now()->startOfMonth();
        $end = now()->startOfMonth()->addDays(6);

        $payroll1 = $this->service->generate($employee, $start, $end);
        $payroll1 = $this->service->approve($payroll1);
        $this->assertEquals(PayrollStatus::APPROVED->value, $payroll1->status->value);

        $payroll2 = $this->service->generate($employee, $start, $end);
        $this->assertEquals($payroll1->id, $payroll2->id);
        $this->assertEquals(PayrollStatus::APPROVED->value, $payroll2->status->value);
    }

    public function test_it_generates_only_for_active_employees(): void
    {
        $active1 = Employee::factory()->create(['status' => \App\Enums\Status\EmployeeStatus::ACTIVE]);
        $active2 = Employee::factory()->create(['status' => \App\Enums\Status\EmployeeStatus::ACTIVE]);
        Employee::factory()->create(['status' => \App\Enums\Status\EmployeeStatus::INACTIVE]);

        $start = now()->startOfMonth();
        $end = now()->startOfMonth()->addDays(6);

        $results = $this->service->generateForAllEmployees($start, $end);

        $this->assertCount(2, $results);
    }

    public function test_approve_reject_workflow(): void
    {
        $employee = Employee::factory()->create();
        $start = now()->startOfMonth();
        $end = now()->startOfMonth()->addDays(6);

        $payroll = $this->service->generate($employee, $start, $end);
        $this->assertEquals(PayrollStatus::PENDING->value, $payroll->status->value);

        $payroll = $this->service->reject($payroll);
        $this->assertEquals(PayrollStatus::REJECTED->value, $payroll->status->value);

        $this->expectException(\InvalidArgumentException::class);
        $this->service->approve($payroll);
    }
}
