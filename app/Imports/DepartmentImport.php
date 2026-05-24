<?php
declare(strict_types=1);
namespace App\Imports;
use App\Models\Department;
use Illuminate\Support\Facades\Log;
use PiaCore\Contracts\Import\ImportHandler;

class DepartmentImport implements ImportHandler
{
    public function prepareRow(array $row): array
    {
        return [
            'name' => $row['Name'] ?? '',
        ];
    }

    public function importRow(array $data): array
    {
        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Name is required.'];
        }

        try {
            $dept = Department::where('name', $data['name'])->first();

            if ($dept) {
                $dept->update($data);
                Log::channel('imports')->info("[DepartmentImport] Updated department {$dept->id}: {$data['name']}");
            } else {
                $dept = Department::create($data);
                Log::channel('imports')->info("[DepartmentImport] Created department {$dept->id}: {$data['name']}");
            }

            return ['success' => true];
        } catch (\Exception $e) {
            Log::channel('imports')->error("[DepartmentImport] Error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
