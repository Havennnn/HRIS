<?php

namespace App\Services\Api\V1\Employee;

use App\Http\Requests\Api\V1\Employee\EmployeeDocumentUploadRequest;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Exception;
use Illuminate\Support\Facades\DB;
use PiaCore\Facades\FileUploader;

class EmployeeApiService
{
    /**
     * Retrieve authenticated employee profile with relations.
     *
     * @throws Exception
     */
    public function getEmployeeProfile(?Employee $employee): Employee
    {
        if (! $employee instanceof Employee) {
            throw new Exception('Unauthorized.');
        }

        try {
            return $employee->load(['position.department', 'contact', 'device']);
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve employee profile: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Upload onboarding documents for the authenticated employee.
     *
     * @throws Exception
     */
    public function uploadDocuments(?Employee $employee, EmployeeDocumentUploadRequest $request): EmployeeDocument
    {
        if (! $employee instanceof Employee) {
            throw new Exception('Unauthorized.');
        }

        try {
            return DB::transaction(function () use ($employee, $request): EmployeeDocument {
                $document = EmployeeDocument::query()->firstOrCreate([
                    'employee_id' => $employee->id,
                ]);

                $updates = [];

                // Handle SSS file
                if ($request->hasFile('sss_file')) {
                    $updates['sss_id'] = $this->uploadFile(
                        $document,
                        $document->sssFile,
                        $request->file('sss_file'),
                        'employee-documents/sss'
                    );
                }

                // Handle PhilHealth file
                if ($request->hasFile('philhealth_file')) {
                    $updates['philhealth_id'] = $this->uploadFile(
                        $document,
                        $document->philhealthFile,
                        $request->file('philhealth_file'),
                        'employee-documents/philhealth'
                    );
                }

                // Handle BIR file
                if ($request->hasFile('bir_file')) {
                    $updates['bir_id'] = $this->uploadFile(
                        $document,
                        $document->birFile,
                        $request->file('bir_file'),
                        'employee-documents/bir'
                    );
                }

                // Handle Medical file
                if ($request->hasFile('medical_file')) {
                    $updates['medical_id'] = $this->uploadFile(
                        $document,
                        $document->medicalFile,
                        $request->file('medical_file'),
                        'employee-documents/medical'
                    );
                }

                if (! empty($updates)) {
                    $document->update($updates);
                }

                return $document->fresh(['sssFile', 'philhealthFile', 'birFile', 'medicalFile']);
            });
        } catch (Exception $e) {
            throw new Exception('Failed to upload documents: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * List onboarding document previews for the authenticated employee.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function getDocuments(?Employee $employee): array
    {
        if (! $employee instanceof Employee) {
            throw new Exception('Unauthorized.');
        }

        try {
            $document = EmployeeDocument::query()
                ->where('employee_id', $employee->id)
                ->with(['sssFile', 'philhealthFile', 'birFile', 'medicalFile'])
                ->first();

            return [
                'sss' => $document?->sssFile?->preview(),
                'philhealth' => $document?->philhealthFile?->preview(),
                'bir' => $document?->birFile?->preview(),
                'medical' => $document?->medicalFile?->preview(),
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve employee documents: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Upload a single file and return the uploaded file ID.
     *
     * @param  EmployeeDocument  $document
     * @param  mixed  $existingFile
     * @param  mixed  $file
     * @param  string  $directory
     * @return int
     *
     * @throws Exception
     */
    private function uploadFile(EmployeeDocument $document, $existingFile, $file, string $directory): int
    {
        try {
            // Release existing file if present
            if ($existingFile) {
                $existingFile->release();
            }

            // Upload new file
            $uploadedFile = FileUploader::upload(
                $file,
                $document,
                ['directory' => $directory]
            );

            return $uploadedFile->id;
        } catch (Exception $e) {
            throw new Exception("Failed to upload file to {$directory}: ".$e->getMessage(), 0, $e);
        }
    }
}
