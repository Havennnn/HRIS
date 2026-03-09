<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PiaCore\Models\Admin;
use PiaCore\Models\Concerns\HasActivityLogs;

class ApplicationInterview extends Model
{
    use HasFactory;
    use HasActivityLogs;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */

    protected $fillable = [
        'application_id',
        'scheduled_at',
        'location',
        'interviewer_id',
        'notes',
        'score',
        'feedback',
        'result',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'score' => 'integer',
    ];

    /**
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'interviewer_id');
    }

    /**
     * --------------------------------------------------------------------------
     * Scopes
     * --------------------------------------------------------------------------
     */

    public function scopePending($query)
    {
        return $query->where('result', 'pending');
    }

    public function scopePassed($query)
    {
        return $query->where('result', 'passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('result', 'failed');
    }

    /**
     * --------------------------------------------------------------------------
     * Helpers
     * --------------------------------------------------------------------------
     */

    public function isPassed(): bool
    {
        return $this->result === 'passed';
    }

    public function isFailed(): bool
    {
        return $this->result === 'failed';
    }

    public function isPending(): bool
    {
        return $this->result === 'pending';
    }
}
