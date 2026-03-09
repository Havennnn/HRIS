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
}
