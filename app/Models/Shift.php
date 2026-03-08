<?php

namespace App\Models;

use App\Enums\Type\ShiftType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PiaCore\Models\Concerns\HasActivityLogs;

class Shift extends Model
{
    use HasFactory;
    use HasActivityLogs;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    
    protected $fillable = [
        'employee_id',
        'type',
        'start',
        'end',
    ];

    protected $casts = [
        'type' => ShiftType::class,
        'start' => 'datetime:H:i',
        'end' => 'datetime:H:i',
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
