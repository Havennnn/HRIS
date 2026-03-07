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
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Models\Concerns\HasArchives;

class Employee extends Model
{
    use HasFactory;
    use HasActivityLogs;
    use HasArchives;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'position_id',
        'first_name',
        'last_name',
        'middle_name',
        'birthdate',
        'mobile_number',
        'email',
        'status',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthdate' => 'date',
        'status' => EmployeeStatus::class,
        'type' => EmployeeType::class,
    ];

    /**
     * Get the position that owns the employee.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get the employee's documents.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    /**
     * Get the employee's contacts.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(EmployeeContact::class);
    }

    /**
     * Get the employee's tools.
     */
    public function tools(): HasOne
    {
        return $this->hasOne(EmployeeTool::class);
    }

    /**
     * Get the employee's devices.
     */
    public function devices(): HasOne
    {
        return $this->hasOne(EmployeeDevice::class);
    }

    /**
     * Get the employee's shift.
     */
    public function shift(): HasOne
    {
        return $this->hasOne(Shift::class);
    }

    /**
     * Get the employee's attendance logs.
     */
    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    /**
     * Get the employee's attendances.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the employee's leave requests.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get the employee's payroll records.
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}
