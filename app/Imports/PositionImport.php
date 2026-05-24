<?php
declare(strict_types=1);
namespace App\Imports;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Log;
use PiaCore\Contracts\Import\ImportHandler;

class PositionImport implements ImportHandler
{
    public function prepareRow(array $row): array
    {
        $data = [
            'name' => $row['Name'] ?? '',
            'level' => $row['Level'] ?? '',
            'salary' => $row['Salary'] ?? '',
            'allowance' => $row['Allowance'] ?? '',
        ];

        // Resolve department name → ID
        if (!empty($row['Department'])) {
            $dept = Department::where('name', $row['Department'])->first();
            $data['department_id'] = $dept?->id;
        }

        return $data;
    }

    public function importRow(array $data): array
    {
        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Name is required.'];
        }

        try {
            $position = Position::where('name', $data['name'])
                ->where('department_id', $data['department_id'] ?? null)
                ->first();

            if ($position) {
                $position->update($data);
                Log::channel('imports')->info("[PositionImport] Updated position {$position->id}: {$data['name']}");
            } else {
                $position = Position::create($data);
                Log::channel('imports')->info("[PositionImport] Created position {$position->id}: {$data['name']}");
            }

            return ['success' => true];
        } catch (\Exception $e) {
            Log::channel('imports')->error("[PositionImport] Error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
