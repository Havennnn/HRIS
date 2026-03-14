<?php

namespace Database\Seeders;

use App\Enums\Status\RequestStatus;
use App\Enums\Type\RequestType;
use App\Models\Employee;
use App\Models\Request;
use Illuminate\Database\Seeder;

class RequestSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        // Create some pending leave requests
        $leaveRequests = [
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::LEAVE,
                'status' => RequestStatus::PENDING,
                'message' => 'Family emergency - need to take care of some personal matters',
                'requested_date' => now()->addDays(5)->toDateString(),
                'days' => 2,
                'end_date' => now()->addDays(6)->toDateString(),
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::LEAVE,
                'status' => RequestStatus::PENDING,
                'message' => 'Annual leave for vacation',
                'requested_date' => now()->addDays(10)->toDateString(),
                'days' => 5,
                'end_date' => now()->addDays(14)->toDateString(),
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::LEAVE,
                'status' => RequestStatus::PENDING,
                'message' => 'Medical appointment',
                'requested_date' => now()->addDays(3)->toDateString(),
                'days' => 1,
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::LEAVE,
                'status' => RequestStatus::PENDING,
                'message' => 'Personal leave',
                'requested_date' => now()->addDays(7)->toDateString(),
                'days' => 3,
                'end_date' => now()->addDays(9)->toDateString(),
            ],
        ];

        foreach ($leaveRequests as $request) {
            Request::query()->create($request);
        }

        // Create some approved leave requests
        $approvedLeaveRequests = [
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::LEAVE,
                'status' => RequestStatus::APPROVED,
                'message' => 'Sick leave - doctor appointment',
                'requested_date' => now()->subDays(5)->toDateString(),
                'days' => 1,
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::LEAVE,
                'status' => RequestStatus::APPROVED,
                'message' => 'Vacation leave approved',
                'requested_date' => now()->subDays(10)->toDateString(),
                'days' => 7,
                'end_date' => now()->subDays(4)->toDateString(),
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::LEAVE,
                'status' => RequestStatus::APPROVED,
                'message' => 'Bereavement leave',
                'requested_date' => now()->subDays(15)->toDateString(),
                'days' => 3,
                'end_date' => now()->subDays(13)->toDateString(),
            ],
        ];

        foreach ($approvedLeaveRequests as $request) {
            Request::query()->create($request);
        }

        // Create some rejected leave requests
        $rejectedLeaveRequests = [
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::LEAVE,
                'status' => RequestStatus::REJECTED,
                'message' => 'Leave request rejected due to workload',
                'requested_date' => now()->subDays(20)->toDateString(),
                'days' => 5,
                'end_date' => now()->subDays(16)->toDateString(),
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::LEAVE,
                'status' => RequestStatus::REJECTED,
                'message' => 'Insufficient leave balance',
                'requested_date' => now()->subDays(25)->toDateString(),
                'days' => 10,
                'end_date' => now()->subDays(16)->toDateString(),
            ],
        ];

        foreach ($rejectedLeaveRequests as $request) {
            Request::query()->create($request);
        }

        // Create pending overtime requests
        $overtimeRequests = [
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::OVERTIME,
                'status' => RequestStatus::PENDING,
                'message' => 'Need to complete urgent project deliverables',
                'requested_date' => now()->subDays(2)->toDateString(),
                'overtime_hours' => 3.5,
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::OVERTIME,
                'status' => RequestStatus::PENDING,
                'message' => 'Server maintenance after office hours',
                'requested_date' => now()->subDays(1)->toDateString(),
                'overtime_hours' => 4.0,
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::OVERTIME,
                'status' => RequestStatus::PENDING,
                'message' => 'Client presentation preparation',
                'requested_date' => now()->addDays(1)->toDateString(),
                'overtime_hours' => 2.5,
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::OVERTIME,
                'status' => RequestStatus::PENDING,
                'message' => 'End of month reporting',
                'requested_date' => now()->toDateString(),
                'overtime_hours' => 3.0,
            ],
        ];

        foreach ($overtimeRequests as $request) {
            Request::query()->create($request);
        }

        // Create approved overtime requests
        $approvedOvertimeRequests = [
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::OVERTIME,
                'status' => RequestStatus::APPROVED,
                'message' => 'Product launch preparation',
                'requested_date' => now()->subDays(7)->toDateString(),
                'overtime_hours' => 5.0,
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::OVERTIME,
                'status' => RequestStatus::APPROVED,
                'message' => 'Database migration work',
                'requested_date' => now()->subDays(3)->toDateString(),
                'overtime_hours' => 4.5,
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::OVERTIME,
                'status' => RequestStatus::APPROVED,
                'message' => 'Critical bug fixes',
                'requested_date' => now()->subDays(1)->toDateString(),
                'overtime_hours' => 2.0,
            ],
        ];

        foreach ($approvedOvertimeRequests as $request) {
            Request::query()->create($request);
        }

        // Create rejected overtime requests
        $rejectedOvertimeRequests = [
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::OVERTIME,
                'status' => RequestStatus::REJECTED,
                'message' => 'Not approved - no proper justification',
                'requested_date' => now()->subDays(10)->toDateString(),
                'overtime_hours' => 8.0,
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::OVERTIME,
                'status' => RequestStatus::REJECTED,
                'message' => 'Budget constraints',
                'requested_date' => now()->subDays(15)->toDateString(),
                'overtime_hours' => 6.0,
            ],
        ];

        foreach ($rejectedOvertimeRequests as $request) {
            Request::query()->create($request);
        }

        // Create cancelled requests
        $cancelledRequests = [
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::LEAVE,
                'status' => RequestStatus::CANCELLED,
                'message' => 'Cancelled by employee - personal reasons',
                'requested_date' => now()->subDays(30)->toDateString(),
                'days' => 3,
                'end_date' => now()->subDays(28)->toDateString(),
            ],
            [
                'employee_id' => $employees->random()->id,
                'type' => RequestType::OVERTIME,
                'status' => RequestStatus::CANCELLED,
                'message' => 'Cancelled - project timeline changed',
                'requested_date' => now()->subDays(12)->toDateString(),
                'overtime_hours' => 4.0,
            ],
        ];

        foreach ($cancelledRequests as $request) {
            Request::query()->create($request);
        }
    }
}
