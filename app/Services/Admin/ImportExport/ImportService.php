<?php

declare(strict_types=1);

namespace App\Services\Admin\ImportExport;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SplFileObject;

class ImportService
{
    /**
     * Expected CSV headers for employee import.
     */
    public const CSV_HEADERS = [
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'position_id',
        'status',
    ];

    /**
     * Import employees from a CSV file.
     *
     * Creates new employees or updates existing ones by email.
     *
     * @return array{success: int, errors: array<int, string>}
     */
    public function importEmployees(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $success = 0;
        $errors = [];

        $csv = new SplFileObject($path, 'r');
        $csv->setFlags(
            SplFileObject::READ_CSV |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::DROP_NEW_LINE
        );

        // Read and validate headers
        $headers = $csv->current();
        if ($headers === false || $headers === [null]) {
            return [
                'success' => 0,
                'errors' => [1 => 'CSV file is empty or has no headers'],
            ];
        }

        $csv->next();

        $headerCount = count($headers);
        $rowNumber = 1; // header row

        DB::beginTransaction();

        try {
            while (! $csv->eof()) {
                $rowNumber++;

                $row = $csv->fgetcsv();

                // Skip empty rows
                if ($row === false || $row === [null] || (count($row) === 1 && trim($row[0] ?? '') === '')) {
                    continue;
                }

                // Pad row to match header count
                $row = array_pad($row, $headerCount, '');

                $data = array_combine($headers, $row);

                if ($data === false) {
                    $errors[$rowNumber] = 'Column count mismatch';
                    continue;
                }

                // Trim all values
                $data = array_map('trim', $data);

                // Validate required fields
                if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email'])) {
                    $errors[$rowNumber] = 'Missing required fields: first_name, last_name, and email are required';
                    continue;
                }

                // Validate email format
                if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[$rowNumber] = "Invalid email format: {$data['email']}";
                    continue;
                }

                // Validate position_id if provided
                if (! empty($data['position_id']) && ! Position::where('id', $data['position_id'])->exists()) {
                    $errors[$rowNumber] = "Position ID {$data['position_id']} does not exist";
                    continue;
                }

                // Normalise status
                $status = ! empty($data['status']) ? (int) $data['status'] : 1;

                try {
                    // Idempotent: update by email if exists
                    $employee = Employee::where('email', $data['email'])->first();

                    $employeeData = [
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'email' => $data['email'],
                        'mobile_number' => $data['mobile_number'] ?? null,
                        'position_id' => ! empty($data['position_id']) ? (int) $data['position_id'] : null,
                        'status' => $status,
                    ];

                    if ($employee) {
                        $employee->update($employeeData);
                        Log::info("[Import] Updated employee ID {$employee->id}: {$data['email']}");
                    } else {
                        $employee = Employee::create($employeeData);
                        Log::info("[Import] Created employee ID {$employee->id}: {$data['email']}");
                    }

                    $success++;
                } catch (\Exception $e) {
                    $errors[$rowNumber] = 'Database error: ' . $e->getMessage();
                    Log::error("[Import] Error on row {$rowNumber}: {$e->getMessage()}");
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[Import] Transaction rolled back: {$e->getMessage()}");

            return [
                'success' => $success,
                'errors' => [0 => 'Transaction failed: ' . $e->getMessage()],
            ];
        }

        Log::info("[Import] Completed: {$success} rows imported, " . count($errors) . ' errors');

        return [
            'success' => $success,
            'errors' => $errors,
        ];
    }
}
