<?php

namespace App\Models;

use App\Enums\Status\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Models\Concerns\HasArchives;

class Application extends Model
{
    use HasActivityLogs;
    use HasArchives;
    use HasFactory;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    protected $fillable = [
        'career_id',
        'first_name',
        'last_name',
        'middle_name',
        'birthdate',
        'mobile_number',
        'email',
        'status',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'status' => ApplicationStatus::class,
    ];

    /**
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function details(): HasOne
    {
        return $this->hasOne(ApplicationDetail::class);
    }

    public function interview(): HasOne
    {
        return $this->hasOne(ApplicationInterview::class);
    }

    /**
     * --------------------------------------------------------------------------
     * Accessors
     * --------------------------------------------------------------------------
     */
    public function getFullNameAttribute(): string
    {
        $middleInitial = $this->middle_name
            ? strtoupper(substr($this->middle_name, 0, 1)).'.'
            : null;

        return trim("{$this->first_name} {$middleInitial} {$this->last_name}");
    }
}
