<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Models\Concerns\HasArchives;

class Career extends Model
{
    use HasFactory;
    use HasArchives;
    use HasActivityLogs;

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
