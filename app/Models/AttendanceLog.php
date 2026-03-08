<?php

namespace App\Models;

use App\Enums\Type\AttendanceLogType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PiaCore\Search\SearchConfig;

class AttendanceLog extends Model
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
        'type',
        'timestamp',
    ];

    protected $casts = [
        'type' => AttendanceLogType::class,
        'timestamp' => 'datetime',
    ];

    protected array $searchable = [
        'type',
        'timestamp',
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
