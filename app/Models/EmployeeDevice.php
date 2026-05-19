<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDevice extends Model
{
    use HasFactory;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    protected $fillable = [
        'employee_id',
        'desktop',
        'laptop',
    ];

    protected $casts = [
        'desktop' => 'string',
        'laptop' => 'string',
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
