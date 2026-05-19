<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Models\Concerns\HasArchives;
use PiaCore\Search\SearchConfig;

class Career extends Model
{
    use HasActivityLogs;
    use HasArchives;
    use HasFactory;
    use SearchConfig;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    protected $fillable = [
        'position_id',
        'description',
        'is_active',
    ];

    protected array $searchable = [
        'description',
        'position.name',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
