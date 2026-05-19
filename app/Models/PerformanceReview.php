<?php

namespace App\Models;

use App\Enums\Status\PerformanceReviewStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Search\SearchConfig;

class PerformanceReview extends Model
{
    use HasActivityLogs;
    use HasFactory;
    use SearchConfig;

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
        'status' => PerformanceReviewStatus::class,
    ];

    protected array $searchable = [
        'employee.first_name',
        'employee.last_name',
        'reviewer.first_name',
        'reviewer.last_name',
    ];

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
