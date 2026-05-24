<?php

namespace App\Imports;

use App\Enums\Status\EmployeeStatus;
use App\Enums\Type\EmployeeType;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Support\Facades\Log;
use PiaCore\Contracts\Import\ImportHandler;

class EmployeeImport implements ImportHandler
{
    /**
     * Transform display values to DB values.
     * Maps file headers (display names) to DB fields.
     */
    public function prepareRow(array $row): array
    {
        $data = [
            'first_name' => $row['First Name'] ?? '',
            'last_name' => $row['Last Name'] ?? '',
            'email' => $row['Email'] ?? '',
            'mobile_number' => $row['Mobile Number'] ?? '',
            'birthdate' => $row['Birthdate'] ?? '',
            'position' => $row['Position'] ?? '',
            'type' => $row['Type'] ?? '',
            'status' => $row['Status'] ?? '',
        ];

        // Position: display name → ID
        if (! empty($data['position'])) {
            $data['position_id'] = $this->resolvePositionId($data['position']);
        }
        unset($data['position']);

        // Birthdate: normalize to Y-m-d
        if (! empty($data['birthdate'])) {
            foreach (['Y-m-d', 'm/d/Y', 'm-d-Y', 'Y/m/d'] as $format) {
                $d = \DateTime::createFromFormat($format, $data['birthdate']);
                if ($d) {
                    $data['birthdate'] = $d->format('Y-m-d');
                    break;
                }
            }
        }

        // Status & Type: label (or raw value) → integer
        $data['status'] = $this->resolveEnum($data['status'] ?? null, EmployeeStatus::class, EmployeeStatus::ACTIVE->value);
        $data['type'] = $this->resolveEnum($data['type'] ?? null, EmployeeType::class, EmployeeType::REGULAR->value);

        return $data;
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
                'birthdate' => $data['birthdate'] ?? null,
                'position_id' => ! empty($data['position_id']) ? (int) $data['position_id'] : null,
                'type' => $data['type'] ?? EmployeeType::REGULAR->value,
                'status' => $data['status'] ?? EmployeeStatus::ACTIVE->value,
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
     * Resolve an enum from either a label ("Regular") or a raw value ("1").
     */
    private function resolveEnum(mixed $value, string $enumClass, int $default): int
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_int($value) || (is_numeric($value) && (int) $value > 0)) {
            return (int) $value;
        }

        static $maps = [];
        if (! isset($maps[$enumClass])) {
            $maps[$enumClass] = collect($enumClass::options())
                ->mapWithKeys(fn (array $o) => [strtolower($o['label']) => $o['value']])
                ->all();
        }

        return $maps[$enumClass][strtolower(trim((string) $value))] ?? $default;
    }

    /**
     * Resolve a position display name to its DB ID.
     * Cached via static so position names are only fetched once per job.
     */
    private function resolvePositionId(string $displayName): ?int
    {
        static $positions = [];

        if (empty($positions)) {
            $positions = Position::query()
                ->with('department')
                ->get()
                ->mapWithKeys(fn ($p) => [
                    $p->name . ($p->department ? " ({$p->department->name})" : '') => $p->id,
                ])
                ->all();
        }

        return $positions[$displayName] ?? null;
    }
}
