<?php

namespace App\Services\Admin\Request;

use App\Enums\Status\AttendanceStatus;
use App\Enums\Status\RequestStatus;
use App\Enums\Type\RequestType;
use App\Models\Attendance;
use App\Models\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use PiaCore\Actions\Options\ListConfig;
use PiaCore\Contracts\CrudService\ListsRecords;
use PiaCore\Contracts\CrudService\ShowsRecords;

class RequestService implements ListsRecords, ShowsRecords
{
    /**
     * @return array{
     *   tabs: array<string, array{countKey?: string|null, scope?: callable(Builder, HttpRequest): (Builder|void)}>,
     *   filters: array<string, string|callable(Builder, mixed): (Builder|void)|array{column?: string}>,
     *   sorts: array<string, string|array{column?: string}>
     *   range: array<string, string|array{startColumn?: string, endColumn?: string}>
     * }
     */
    public function list(Model|string|Relation $model, HttpRequest $request): ListConfig
    {
        return (new ListConfig)
            ->baseQuery(function (Builder $query): Builder {
                return $query->with(['employee', 'employee.position']);
            })
            ->tabs([
                'default' => [
                    'countKey' => 'defaultCount',
                ],
                'leaves' => [
                    'countKey' => 'leavesCount',
                    'scope' => fn (Builder $query) => $query->where('type', RequestType::LEAVE),
                ],
                'overtimes' => [
                    'countKey' => 'overtimesCount',
                    'scope' => fn (Builder $query) => $query->where('type', RequestType::OVERTIME),
                ],
                'archived' => [
                    'countKey' => 'archivedCount',
                    'scope' => fn (Builder $query) => $query->onlyTrashed(),
                ],
            ])
            ->filters([
                'type' => fn (Builder $query, $value) => $query->whereIn('type', $value),
                'status' => fn (Builder $query, $value) => $query->whereIn('status', $value),
            ])
            ->sorts([
                'date' => 'requested_date',
                'created' => 'created_at',
            ])
            ->range([
                'requested' => 'requested_date',
            ]);
    }

    /**
     * Show a single record.
     */
    public function show(Model $record, HttpRequest $request): Model
    {
        // Auto-submit for review when viewing pending requests
        if ($record->status === RequestStatus::PENDING) {
            $this->submitForReview($record);
            $record->refresh();
        }

        return $record->load(['employee', 'employee.position:id,name,level,department_id', 'employee.position.department:id,name']);
    }

    /**
     * Approve a request and apply its effects to attendance.
     */
    public function approve(Request $request): Request
    {
        // Can only approve requests under review
        if ($request->status !== RequestStatus::REVIEWING) {
            throw new \InvalidArgumentException('Only requests under review can be approved.');
        }

        return DB::transaction(function () use ($request) {
            $request->update(['status' => RequestStatus::APPROVED]);

            match ($request->type) {
                RequestType::OVERTIME => $this->handleOvertimeApproval($request),
                RequestType::LEAVE => $this->handleLeaveApproval($request),
                default => null,
            };

            return $request->fresh();
        });
    }

    /**
     * Reject a request.
     */
    public function reject(Request $request): Request
    {
        // Can only reject requests under review
        if ($request->status !== RequestStatus::REVIEWING) {
            throw new \InvalidArgumentException('Only requests under review can be rejected.');
        }

        return DB::transaction(function () use ($request) {
            $request->update(['status' => RequestStatus::REJECTED]);

            // Remove any attendance effects if the request was previously approved
            if ($request->type === RequestType::OVERTIME) {
                $this->removeOvertimeFromAttendance($request);
            }

            return $request->fresh();
        });
    }

    /**
     * Handle overtime approval - add overtime hours to attendance.
     */
    protected function handleOvertimeApproval(Request $request): void
    {
        $date = $request->requested_date;
        $overtimeMinutes = ($request->overtime_hours ?? 0) * 60;

        // Find or create attendance for the date
        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date' => $date,
            ],
            [
                'time_in' => null,
                'time_out' => null,
                'late_minutes' => 0,
                'overtime_minutes' => 0,
                'status' => AttendanceStatus::OVERTIME,
            ]
        );

        // Add overtime to the attendance record
        $attendance->update([
            'overtime_minutes' => $overtimeMinutes,
            'request_id' => $request->id,
            'status' => AttendanceStatus::OVERTIME,
        ]);
    }

    /**
     * Handle leave approval - mark attendance during leave period.
     */
    protected function handleLeaveApproval(Request $request): void
    {
        $startDate = $request->requested_date;
        $endDate = $request->end_date ?? $request->requested_date;

        // Update all attendance records in the leave period
        Attendance::where('employee_id', $request->employee_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->update([
                'request_id' => $request->id,
                // Status remains as-is, but the request_id links the attendance to the approved leave
                // This allows the payroll system to exclude these days from regular pay calculation
            ]);
    }

    /**
     * Remove overtime from attendance when request is rejected or cancelled.
     */
    protected function removeOvertimeFromAttendance(Request $request): void
    {
        Attendance::where('employee_id', $request->employee_id)
            ->where('date', $request->requested_date)
            ->where('request_id', $request->id)
            ->update([
                'overtime_minutes' => 0,
                'request_id' => null,
            ]);
    }

    /**
     * Handle request cancellation.
     */
    public function cancel(Request $request): Request
    {
        // Can only cancel approved requests
        if ($request->status !== RequestStatus::APPROVED) {
            throw new \InvalidArgumentException('Only approved requests can be cancelled.');
        }

        return DB::transaction(function () use ($request) {
            $request->update(['status' => RequestStatus::CANCELLED]);

            // Remove any attendance effects
            if ($request->type === RequestType::OVERTIME) {
                $this->removeOvertimeFromAttendance($request);
            }

            // For leave, we keep the request_id but the status is cancelled
            // Payroll can check both status and request_id to determine how to handle

            return $request->fresh();
        });
    }

    /**
     * Submit request for review (pending -> reviewing).
     */
    public function submitForReview(Request $request): Request
    {
        // Can only submit pending requests for review
        if ($request->status !== RequestStatus::PENDING) {
            throw new \InvalidArgumentException('Only pending requests can be submitted for review.');
        }

        $request->update(['status' => RequestStatus::REVIEWING]);

        return $request->fresh();
    }

    /**
     * Complete an approved request.
     */
    public function complete(Request $request): Request
    {
        // Can only complete approved requests
        if ($request->status !== RequestStatus::APPROVED) {
            throw new \InvalidArgumentException('Only approved requests can be completed.');
        }

        $request->update(['status' => RequestStatus::COMPLETED]);

        return $request->fresh();
    }
}
