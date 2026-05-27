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

    public function store(string $modelClass, FormRequest $request): Model
    {
        $record = $modelClass::query()->create(
            $this->extractModelData($modelClass, $request)
        );

        $this->syncSeoMeta($record, $request);

        return $record;
    }

    public function update(Model $record, FormRequest $request): Model
    {
        $record->update(
            $this->extractModelData($record, $request)
        );

        $this->syncSeoMeta($record, $request);

        return $record->fresh();
    }

    protected function extractModelData(Model|string $model, FormRequest $request): array
    {
        $record = is_string($model) ? new $model : $model;

        return [
            'position_id' => $request->validated('position_id', $record->position_id),
            'description' => $request->validated('description', $record->description),
            'salary' => $request->validated('salary', $record->salary),
            'status' => $request->validated('status', CareerStatus::PUBLISHED->value),
        ];
    }

    protected function syncSeoMeta(Model $record, FormRequest $request): void
    {
        $title = $request->validated('meta_title');
        $description = $request->validated('meta_description');
        $ogTitle = $request->validated('og_title');
        $ogDescription = $request->validated('og_description');
        $ogImage = $request->validated('og_image');

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
