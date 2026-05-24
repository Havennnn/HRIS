<?php
declare(strict_types=1);
namespace App\Imports;
use App\Enums\Type\HolidayType;
use App\Models\Holiday;
use Illuminate\Support\Facades\Log;
use PiaCore\Contracts\Import\ImportHandler;

class HolidayImport implements ImportHandler
{
    public function prepareRow(array $row): array
    {
        $data = [
            'name' => $row['Name'] ?? '',
            'date' => $row['Date'] ?? '',
            'description' => $row['Description'] ?? '',
            'is_paid' => true,
        ];

        // Type: label → enum value
        if (!empty($row['Type'])) {
            $typeMap = collect(HolidayType::options())
                ->mapWithKeys(fn(array $o) => [strtolower($o['label']) => $o['value']])
                ->all();
            $key = strtolower(trim($row['Type']));
            $data['type'] = $typeMap[$key] ?? HolidayType::REGULAR->value;
        }

        // Is Paid: label → boolean
        if (!empty($row['Is Paid'])) {
            $data['is_paid'] = in_array(strtolower($row['Is Paid']), ['yes', 'true', '1', 'y'], true);
        }

        // Date: normalize
        if (!empty($data['date'])) {
            foreach (['Y-m-d', 'm/d/Y', 'm-d-Y'] as $format) {
                $d = \DateTime::createFromFormat($format, $data['date']);
                if ($d) { $data['date'] = $d->format('Y-m-d'); break; }
            }
        }

        return $data;
    }

    public function importRow(array $data): array
    {
        if (empty($data['name']) || empty($data['date'])) {
            return ['success' => false, 'message' => 'Name and Date are required.'];
        }

        try {
            $holiday = Holiday::where('name', $data['name'])
                ->where('date', $data['date'])
                ->first();

            if ($holiday) {
                $holiday->update($data);
                Log::channel('imports')->info("[HolidayImport] Updated holiday {$holiday->id}: {$data['name']}");
            } else {
                $holiday = Holiday::create($data);
                Log::channel('imports')->info("[HolidayImport] Created holiday {$holiday->id}: {$data['name']}");
            }

            return ['success' => true];
        } catch (\Exception $e) {
            Log::channel('imports')->error("[HolidayImport] Error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
