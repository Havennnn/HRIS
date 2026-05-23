<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PiaCore\Search\SearchConfig;

class Attendance extends Model
{
    use HasFactory;
    use SearchConfig;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    protected $fillable = [
        'employee_id',
        'time_in',
        'time_out',
        'late_minutes',
        'overtime_minutes',
        'date',
        'request_id',
        'status',
    ];

    protected $casts = [
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
        'date' => 'date',
        'status' => \App\Enums\Status\AttendanceStatus::class,
    ];

    protected array $searchable = [
        'date',
        'time_in',
        'time_out',
        'late_minutes',
        'overtime_minutes',
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

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(AttendanceTag::class, 'attendance_attendance_tags');
    }
}
