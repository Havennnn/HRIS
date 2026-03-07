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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'type',
        'start',
        'end',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => ShiftType::class,
        'start' => 'datetime:H:i',
        'end' => 'datetime:H:i',
    ];

    /**
     * Get the employee that owns the shift.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
