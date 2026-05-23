<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Employee;
use App\Models\Position;
use App\Notifications\ImportCompleted;
use App\Notifications\ImportFailed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PiaCore\Contracts\Import\ImportHandler;

class EmployeeImport implements ImportHandler
{
    /** @var array<string, int> Cached position name → ID mapping */
    private array $positionMap = [];

    /**
     * Transform display values to DB values.
     * e.g. "Software Engineer (Engineering)" → position_id = 5
     *      "Active" → status = 1
     *      "Regular" → type = 1
     */
    public function prepareRow(array $row): array
    {
        if (! empty($row['position'])) {
            $row['position_id'] = $this->resolvePositionId($row['position']);
        }
        unset($row['position']);

        if (! empty($row['status'])) {
            $row['status'] = match (strtolower($row['status'])) {
                'active' => 1,
                'inactive' => 2,
                'resigned' => 3,
                'terminated' => 4,
                default => (int) $row['status'],
            };
        }

        if (! empty($row['type'])) {
            $row['type'] = match (strtolower($row['type'])) {
                'regular' => 1,
                'probationary' => 2,
                'contractual' => 3,
                'part-time' => 4,
                default => (int) $row['type'],
            };
        }

        return $row;
    }

    /**
     * Process a single employee row — store to database.
     */
    public function importRow(array $data): array
    {
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email'])) {
            return [
                'success' => false,
                'message' => 'Missing required fields: first_name, last_name, and email',
            ];
        }

        if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => "Invalid email: {$data['email']}",
            ];
        }

        try {
            $employee = Employee::where('email', $data['email'])->first();

            $employeeData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'mobile_number' => $data['mobile_number'] ?? null,
                'position_id' => ! empty($data['position_id']) ? (int) $data['position_id'] : null,
                'status' => $data['status'] ?? 1,
            ];

            if ($employee) {
                $employee->update($employeeData);
                Log::info("[Import] Updated employee {$employee->id}: {$data['email']}");
            } else {
                $employee = Employee::create($employeeData);
                Log::info("[Import] Created employee {$employee->id}: {$data['email']}");
            }

            return ['success' => true];
        } catch (\Exception $e) {
            Log::error("[Import] Error: {$e->getMessage()}");
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Notify the admin with details of what succeeded and what failed.
     */
    public function notify(Request $request, array $result): void
    {
        try {
            $admin = $request->user('admin');
            if (! $admin) {
                return;
            }

            if (! empty($result['errors'])) {
                $admin->notify(new ImportFailed(
                    errorCount: count($result['errors']),
                    errors: $result['errors'],
                    source: 'logic',
                ));
            } else {
                $admin->notify(new ImportCompleted(
                    success: $result['success'],
                    errors: [],
                ));
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send import notification: ' . $e->getMessage());
        }
    }

    private function resolvePositionId(string $displayName): ?int
    {
        if (empty($this->positionMap)) {
            $this->positionMap = Position::query()
                ->with('department')
                ->get()
                ->mapWithKeys(fn ($p) => [
                    $p->name . ($p->department ? " ({$p->department->name})" : '') => $p->id,
                ])
                ->all();
        }

        return $this->positionMap[$displayName] ?? null;
    }
}
