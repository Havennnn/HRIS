<?php

namespace App\Models;

use App\Enums\Status\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

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
        'status',
        'date',
    ];

    protected $casts = [
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
        'status' => AttendanceStatus::class,
        'date' => 'date',
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
}
