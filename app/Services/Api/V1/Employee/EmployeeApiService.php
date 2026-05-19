<?php

namespace App\Services\Api\V1\Employee;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\Kpi;
use App\Models\PerformanceReview;
use App\Traits\BuildsApiResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PiaCore\Facades\FileUploader;

class EmployeeApiService
{
    use BuildsApiResponses;

    public function getEmployeeProfile(Request $request): JsonResponse
    {
        $employee = $request->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse();
        }

        try {
            $employee->load(['position.department', 'contact', 'device']);

            return $this->successResponse(
                (new \App\Http\Resources\Api\V1\Employee\EmployeeProfileResource($employee))->resolve($request)
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve employee profile: '.$e->getMessage(), 500);
        }
    }

    public function uploadDocuments(Request $request): JsonResponse
    {
        $employee = $request->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse();
        }

        try {
            DB::transaction(function () use ($employee, $request): void {
                $document = EmployeeDocument::query()->firstOrCreate([
                    'employee_id' => $employee->id,
                ]);

                $updates = [];

                if ($request->hasFile('sss_file')) {
                    $updates['sss_id'] = $this->uploadFile(
                        $document,
                        $document->sssFile,
                        $request->file('sss_file'),
                        'employee-documents/sss'
                    );
                }

                if ($request->hasFile('philhealth_file')) {
                    $updates['philhealth_id'] = $this->uploadFile(
                        $document,
                        $document->philhealthFile,
                        $request->file('philhealth_file'),
                        'employee-documents/philhealth'
                    );
                }

                if ($request->hasFile('bir_file')) {
                    $updates['bir_id'] = $this->uploadFile(
                        $document,
                        $document->birFile,
                        $request->file('bir_file'),
                        'employee-documents/bir'
                    );
                }

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
            });

            return $this->successResponse([], 'File uploaded successfully.');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to upload documents: '.$e->getMessage(), 422);
        }
    }

    public function getDocuments(Request $request): JsonResponse
    {
        $employee = $request->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse();
        }

        try {
            $document = EmployeeDocument::query()
                ->where('employee_id', $employee->id)
                ->with(['sssFile', 'philhealthFile', 'birFile', 'medicalFile'])
                ->first();

            return $this->successResponse([
                'sss' => $document?->sssFile?->preview(),
                'philhealth' => $document?->philhealthFile?->preview(),
                'bir' => $document?->birFile?->preview(),
                'medical' => $document?->medicalFile?->preview(),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve employee documents: '.$e->getMessage(), 500);
        }
    }

    public function getPerformanceMetrics(Request $request): JsonResponse
    {
        $employee = $request->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse();
        }

        try {
            $kpis = Kpi::query()->orderBy('name')->get();

            $latestReview = PerformanceReview::query()
                ->with('reviewScores')
                ->where('employee_id', $employee->id)
                ->latest('review_date')
                ->first();

            $scoredKpiIds = $latestReview?->reviewScores->pluck('kpi_id')->all() ?? [];

            $kpiBreakdown = $kpis->map(function (Kpi $kpi) use ($latestReview, $scoredKpiIds): array {
                $score = in_array($kpi->id, $scoredKpiIds)
                    ? $latestReview->reviewScores->firstWhere('kpi_id', $kpi->id)?->score
                    : null;

                return [
                    'kpi_id' => $kpi->id,
                    'kpi_name' => $kpi->name,
                    'description' => $kpi->description,
                    'score' => $score,
                    'status' => $score !== null ? 'rated' : 'no_data',
                ];
            })->values()->all();

            $ratedScores = array_filter($kpiBreakdown, fn (array $item): bool => $item['status'] === 'rated');
            $ratedCount = count($ratedScores);
            $averageScore = $ratedCount > 0
                ? round(array_sum(array_column($ratedScores, 'score')) / $ratedCount, 2)
                : null;

            return $this->successResponse([
                'latest_review' => $latestReview ? [
                    'id' => $latestReview->id,
                    'review_date' => $latestReview->review_date?->toDateString(),
                    'status' => $latestReview->status?->label(),
                    'overall_score' => $latestReview->overall_score,
                ] : null,
                'kpi_breakdown' => $kpiBreakdown,
                'summary' => [
                    'total_kpis' => count($kpis),
                    'rated_kpis' => $ratedCount,
                    'unrated_kpis' => count($kpis) - $ratedCount,
                    'average_score' => $averageScore,
                ],
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve performance metrics: '.$e->getMessage(), 500);
        }
    }

    private function uploadFile(EmployeeDocument $document, $existingFile, $file, string $directory): int
    {
        if ($existingFile) {
            $existingFile->release();
        }

        $uploadedFile = FileUploader::upload(
            $file,
            $document,
            ['directory' => $directory]
        );

        return $uploadedFile->id;
    }
}
