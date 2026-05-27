<?php

namespace App\Services\Admin\Employee;

use App\Models\Employee;
use App\Models\Kpi;
use App\Models\PerformanceReview;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PiaCore\Actions\Options\ListConfig;
use PiaCore\Contracts\CrudService\ListsRecords;
use PiaCore\Contracts\CrudService\StoresRecords;
use PiaCore\Contracts\CrudService\UpdatesRecords;

class EmployeeService implements ListsRecords, StoresRecords, UpdatesRecords
{
    public function list(Model|string|Relation $model, Request $request): ListConfig
    {
        return (new ListConfig)
            ->baseQuery(function (Builder $query): Builder {
                return $query->with(['position', 'position.department', 'device']);
            })
            ->tabs([
                'default' => [
                    'countKey' => 'defaultCount',
                ],
                'archived' => [
                    'countKey' => 'archivedCount',
                    'scope' => fn (Builder $query) => $query->onlyTrashed(),
                ],
            ])
            ->filters([
                'position' => fn (Builder $query, $value) => $query->whereIn('position_id', $value),
                'status' => fn (Builder $query, $value) => $query->whereIn('status', $value),
                'type' => fn (Builder $query, $value) => $query->whereIn('type', $value),
            ])
            ->sorts([
                'name' => 'first_name',
                'created' => 'created_at',
            ]);
    }

    public function store(string $modelClass, FormRequest $request): Model
    {
        return DB::transaction(function () use ($modelClass, $request) {
            return $modelClass::query()->create(
                $this->prepareStoreData($request)
            );
        });
    }

    public function update(Model $record, FormRequest $request): Model
    {
        return DB::transaction(function () use ($record, $request) {
            $record->update(
                $this->prepareUpdateData($record, $request)
            );

            return $record->fresh();
        });
    }

    protected function prepareStoreData(FormRequest $request): array
    {
        return [
            'position_id' => $request->validated('position_id'),
            'first_name' => $request->validated('first_name'),
            'last_name' => $request->validated('last_name'),
            'middle_name' => $request->validated('middle_name'),
            'birthdate' => $request->validated('birthdate'),
            'mobile_number' => $request->validated('mobile_number'),
            'email' => $request->validated('email'),
            'type' => $request->validated('type'),
        ];
    }

    protected function prepareUpdateData(Model $record, FormRequest $request): array
    {
        return [
            'position_id' => $request->validated('position_id'),
            'first_name' => $request->validated('first_name'),
            'last_name' => $request->validated('last_name'),
            'middle_name' => $request->validated('middle_name'),
            'birthdate' => $request->validated('birthdate'),
            'mobile_number' => $request->validated('mobile_number'),
            'email' => $request->validated('email'),
            'type' => $request->validated('type'),
        ];
    }

    public function updateDevice(Employee $employee, ?FormRequest $request = null): RedirectResponse
    {
        return DB::transaction(function () use ($employee, $request) {
            $employee->device()->updateOrCreate(
                [],
                $this->prepareDeviceData($request)
            );

            return redirect()->back()->with('success', 'Device information updated successfully.');
        });
    }

    public function resetPassword(Employee $employee): RedirectResponse
    {
        $employee->sendPasswordResetLink();

        return redirect()->back()->with('success', 'Password reset link sent successfully to '.$employee->email);
    }

    protected function prepareDeviceData(?FormRequest $request = null): array
    {
        return [
            'desktop' => $request?->validated('desktop') ?? '',
            'laptop' => $request?->validated('laptop') ?? '',
        ];
    }

    public function updateContactPerson(Employee $employee, ?FormRequest $request = null): RedirectResponse
    {
        return DB::transaction(function () use ($employee, $request) {
            $contactData = $this->prepareContactData($request);

            if ($this->isContactDataEmpty($contactData)) {
                $employee->contact()->delete();

                return redirect()->back()->with('success', 'Contact person removed successfully.');
            }

            $employee->contact()->updateOrCreate([], $contactData);

            return redirect()->back()->with('success', 'Contact person updated successfully.');
        });
    }

    protected function prepareContactData(?FormRequest $request = null): array
    {
        return [
            'name' => trim((string) ($request?->validated('name') ?? '')),
            'type' => $request?->validated('type'),
            'mobile_number' => trim((string) ($request?->validated('mobile_number') ?? '')),
        ];
    }

    protected function isContactDataEmpty(array $data): bool
    {
        return blank($data['name'])
            && blank($data['type'])
            && blank($data['mobile_number']);
    }

    public function getPerformanceMetrics(Employee $employee): array
    {
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
        })->all();

        $ratedScores = array_filter($kpiBreakdown, fn (array $item): bool => $item['status'] === 'rated');
        $ratedCount = count($ratedScores);
        $averageScore = $ratedCount > 0
            ? round(array_sum(array_column($ratedScores, 'score')) / $ratedCount, 2)
            : null;

        return [
            'employee' => [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
            ],
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
        ];
    }
}
