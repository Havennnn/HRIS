<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PiaCore\Concerns\HasOptions;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Models\Concerns\HasArchives;
use PiaCore\Search\SearchConfig;

class Position extends Model
{
    use HasFactory;
    use HasArchives;
    use HasActivityLogs;
    use HasOptions;
    use SearchConfig;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */

    protected $fillable = [
        'department_id',
        'name',
        'salary',
        'allowance',
        'level',
    ];

    /**
     * --------------------------------------------------------------------------
     * Search Configuration
     * --------------------------------------------------------------------------
     */

    protected array $searchable = [
        'name',
        'department.name',
    ];

    /**
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * --------------------------------------------------------------------------
     * Ovverrides
     * --------------------------------------------------------------------------
     */
    public static function options(?int $limit = null): array
    {
        $query = static::query();

        if ($limit !== null) {
            $query->limit(max(1, $limit));
        }

        return $query
            ->get(['id', 'name', 'level'])
            ->map(fn ($record) => [
                'value' => $record->id,
                'label' => "{$record->name} - {$record->level}",
            ])
            ->all();
    }
}
