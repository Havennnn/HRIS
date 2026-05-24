<?php
declare(strict_types=1);
namespace App\Imports;
use App\Enums\Status\CareerStatus;
use App\Models\Career;
use App\Models\Position;
use Illuminate\Support\Facades\Log;
use PiaCore\Contracts\Import\ImportHandler;

class CareerImport implements ImportHandler
{
    public function prepareRow(array $row): array
    {
        $data = [
            'description' => $row['Description'] ?? '',
            'salary' => $row['Salary'] ?? '',
        ];

        // Position: display name → ID
        if (!empty($row['Position'])) {
            static $positionMap = [];
            if (empty($positionMap)) {
                $positionMap = Position::query()->with('department')->get()
                    ->mapWithKeys(fn($p) => [$p->name . ($p->department ? " ({$p->department->name})" : '') => $p->id])
                    ->all();
            }
            $data['position_id'] = $positionMap[$row['Position']] ?? null;
        }

        // Status: label → enum value
        if (!empty($row['Status'])) {
            $statusMap = collect(CareerStatus::options())
                ->mapWithKeys(fn(array $o) => [strtolower($o['label']) => $o['value']])
                ->all();
            $key = strtolower(trim($row['Status']));
            $data['status'] = $statusMap[$key] ?? CareerStatus::DRAFT->value;
        } else {
            $data['status'] = CareerStatus::DRAFT->value;
        }

        return $data;
    }

    public function importRow(array $data): array
    {
        if (empty($data['position_id'])) {
            return ['success' => false, 'message' => 'Position is required.'];
        }

        try {
            $career = Career::where('position_id', $data['position_id'])->first();

            if ($career) {
                $career->update($data);
                Log::channel('imports')->info("[CareerImport] Updated career {$career->id}");
            } else {
                $career = Career::create($data);
                Log::channel('imports')->info("[CareerImport] Created career {$career->id}");
            }

            return ['success' => true];
        } catch (\Exception $e) {
            Log::channel('imports')->error("[CareerImport] Error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
