<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewScore extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'performance_review_id',
        'kpi_id',
        'score',
    ];

    /**
     * Get the performance review that owns the score.
     */
    public function performanceReview(): BelongsTo
    {
        return $this->belongsTo(PerformanceReview::class);
    }

    /**
     * Get the KPI that owns the score.
     */
    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class);
    }
}
