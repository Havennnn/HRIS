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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_date',
        'overall_score',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'review_date' => 'date',
        'overall_score' => 'decimal:2',
    ];

    /**
     * Get the employee that owns the review.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the reviewer (manager) that owns the review.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    /**
     * Get the review scores for this review.
     */
    public function reviewScores(): HasMany
    {
        return $this->hasMany(ReviewScore::class);
    }

    /**
     * Get the review feedback for this review.
     */
    public function reviewFeedback(): HasMany
    {
        return $this->hasMany(ReviewFeedback::class);
    }
}
