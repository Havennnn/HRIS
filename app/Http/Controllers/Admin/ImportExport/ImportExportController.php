<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\ImportExport;

use App\Http\Controllers\Controller;
use App\Notifications\ImportCompleted;
use App\Services\Admin\ImportExport\ExportService;
use App\Services\Admin\ImportExport\ImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportController extends Controller
{
    public function __construct(
        protected ImportService $importService,
        protected ExportService $exportService,
    ) {}

    /**
     * Download a CSV template (manifest) showing required columns for import.
     */
    public function downloadManifest(): StreamedResponse
    {
        $headers = ImportService::CSV_HEADERS;
        $exampleRow = [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan@example.com',
            'mobile_number' => '09171234567',
            'position_id' => '1',
            'status' => '1',
        ];

        $stream = fopen('php://temp', 'r+b');
        fwrite($stream, "\xEF\xBB\xBF"); // BOM for Excel
        fputcsv($stream, $headers);
        fputcsv($stream, array_values($exampleRow));
        rewind($stream);

        $csv = stream_get_contents($stream);
        fclose($stream);

        return response()->streamDownload(function () use ($csv): void {
            echo $csv;
        }, 'employee-import-template.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    /**
     * Process the uploaded CSV file for employee import.
     *
     * @return RedirectResponse
     */
    public function processImport(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $file = $request->file('file');

        if ($file === null) {
            return back()->with('error', 'No file was uploaded.');
        }

        $result = $this->importService->importEmployees($file);

        // Send notification to super admins
        try {
            $admins = config('notifications.admin_recipients', []);
            if (! empty($admins)) {
                Notification::route('mail', $admins)
                    ->notify(new ImportCompleted(
                        success: $result['success'],
                        errors: $result['errors'],
                    ));
            }
        } catch (\Exception $e) {
            // Notification failure should not break the import flow
            logger()->warning('Failed to send import notification: ' . $e->getMessage());
        }

        if (empty($result['errors'])) {
            return back()->with('success', "Successfully imported {$result['success']} employee(s).");
        }

        return back()->with('importResult', $result);
    }

    /**
     * Download employees as CSV.
     */
    public function exportEmployees(): StreamedResponse
    {
        $csv = $this->exportService->exportEmployees();

        return $this->streamCsvResponse($csv, 'employees-export.csv');
    }

    /**
     * Download payrolls as CSV within a date range.
     */
    public function exportPayrolls(Request $request): StreamedResponse
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $csv = $this->exportService->exportPayrolls(
            $request->input('start_date'),
            $request->input('end_date')
        );

        return $this->streamCsvResponse($csv, 'payrolls-export.csv');
    }

    /**
     * Download attendance logs as CSV within a date range.
     */
    public function exportAttendanceLogs(Request $request): StreamedResponse
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $csv = $this->exportService->exportAttendanceLogs(
            $request->input('start_date'),
            $request->input('end_date')
        );

        return $this->streamCsvResponse($csv, 'attendance-logs-export.csv');
    }

    /**
     * Return a StreamedResponse with CSV headers.
     */
    private function streamCsvResponse(string $csv, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($csv): void {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
