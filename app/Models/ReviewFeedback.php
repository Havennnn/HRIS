<?php

namespace App\Models;

use App\Enums\Type\ReviewFeedbackType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewFeedback extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'performance_review_id',
        'feedback',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => ReviewFeedbackType::class,
    ];

    /**
     * Get the performance review that owns the feedback.
     */
    public function performanceReview(): BelongsTo
    {
        return $this->belongsTo(PerformanceReview::class);
    }
}
