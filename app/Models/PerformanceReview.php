<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerformanceReview extends Model
{
    use HasFactory;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    
    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_date',
        'overall_score',
        'status',
    ];

    protected $casts = [
        'review_date' => 'date',
        'overall_score' => 'decimal:2',
    ];

    /**
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    public function reviewScores(): HasMany
    {
        return $this->hasMany(ReviewScore::class);
    }

    public function reviewFeedback(): HasMany
    {
        return $this->hasMany(ReviewFeedback::class);
    }
}
