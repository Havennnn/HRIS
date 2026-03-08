<?php

namespace App\Models;

use App\Enums\Status\LeaveRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    
    protected $fillable = [
        'employee_id',
        'message',
        'requested_date',
        'days',
        'end_date',
        'status',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'end_date' => 'date',
        'status' => LeaveRequestStatus::class,
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
