<?php

namespace App\Services\Admin\Career;

use App\Enums\Status\CareerStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use PiaCore\Actions\Options\ListConfig;
use PiaCore\Contracts\CrudService\ListsRecords;
use PiaCore\Contracts\CrudService\StoresRecords;
use PiaCore\Contracts\CrudService\UpdatesRecords;

class CareerService implements ListsRecords, StoresRecords, UpdatesRecords
{
    public function list(Model|string|Relation $model, Request $request): ListConfig
    {
        return (new ListConfig)
            ->baseQuery(fn (Builder $query) => $query->with(['position']))
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
            ])
            ->sorts([
                'created' => 'created_at',
            ])
            ->range([
                'created' => 'created_at',
            ]);
    }

    public function store(string $modelClass, array $payload, ?FormRequest $request = null): Model
    {
        $record = $modelClass::query()->create(
            $this->extractModelData(new $modelClass, $payload)
        );

        $this->syncSeoMeta($record, $payload);

        return $record;
    }

    public function update(Model $record, array $payload, ?FormRequest $request = null): Model
    {
        $record->update(
            $this->extractModelData($record, $payload)
        );

        $this->syncSeoMeta($record, $payload);

        return $record->fresh();
    }

    protected function extractModelData(Model $record, array $data): array
    {
        return [
            'position_id' => $data['position_id'] ?? $record->position_id,
            'description' => $data['description'] ?? $record->description,
            'salary' => $data['salary'] ?? $record->salary,
            'status' => $data['status'] ?? CareerStatus::PUBLISHED->value,
        ];
    }

    protected function syncSeoMeta(Model $record, array $data): void
    {
        $title = $data['meta_title'] ?? null;
        $description = $data['meta_description'] ?? null;
        $ogTitle = $data['og_title'] ?? null;
        $ogDescription = $data['og_description'] ?? null;
        $ogImage = $data['og_image'] ?? null;

        if ($title === null && $description === null && $ogTitle === null && $ogDescription === null && $ogImage === null) {
            return;
        }

        $seoData = [];
        foreach (['meta_title' => $title, 'meta_description' => $description, 'og_title' => $ogTitle, 'og_description' => $ogDescription, 'og_image' => $ogImage] as $field => $value) {
            if ($value !== null) {
                $seoData[$field] = $value;
            }
        }

        $record->seoMeta()->updateOrCreate(
            ['metaable_id' => $record->id, 'metaable_type' => get_class($record)],
            $seoData
        );
    }
}
