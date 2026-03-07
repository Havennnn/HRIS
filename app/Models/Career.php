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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'position_id',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the position that owns the career.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get the applications for this career.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
