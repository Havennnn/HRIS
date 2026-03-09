<?php

namespace App\Models;

use App\Enums\Status\EmployeeStatus;
use App\Enums\Type\EmployeeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use PiaCore\Concerns\HasOptions;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Models\Concerns\HasArchives;
use PiaCore\Search\SearchConfig;

class Employee extends Model
{
    use HasFactory;
    use HasActivityLogs;
    use HasArchives;
    use Notifiable;
    use HasOptions;
    use SearchConfig;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */

    protected $fillable = [
        'position_id',
        'first_name',
        'last_name',
        'middle_name',
        'birthdate',
        'hired_date',
        'mobile_number',
        'email',
        'status',
        'type',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'hired_date' => 'date',
        'status' => EmployeeStatus::class,
        'type' => EmployeeType::class,
    ];

    /**
     * --------------------------------------------------------------------------
     * Search Configuration
     * --------------------------------------------------------------------------
     */

    protected array $searchable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'mobile_number',
        'position.name',
        'position.department.name',
    ];

    /**
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(EmployeeContact::class);
    }

    public function tools(): HasOne
    {
        return $this->hasOne(EmployeeTool::class);
    }

    public function devices(): HasOne
    {
        return $this->hasOne(EmployeeDevice::class);
    }

    public function shift(): HasOne
    {
        return $this->hasOne(Shift::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * --------------------------------------------------------------------------
     * Accessors
     * --------------------------------------------------------------------------
     */

    public function getFullNameAttribute(): string
    {
        $middleInitial = $this->middle_name
            ? strtoupper(substr($this->middle_name, 0, 1)) . '.'
            : null;

        return trim("{$this->first_name} {$middleInitial} {$this->last_name}");
    }
}
