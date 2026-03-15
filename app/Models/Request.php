<?php

namespace App\Models;

use App\Enums\Status\RequestStatus;
use App\Enums\Type\RequestType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PiaCore\Models\Concerns\HasArchives;
use PiaCore\Search\SearchConfig;

class Request extends Model
{
    use HasFactory;
    use HasArchives;
    use SearchConfig;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */

    protected $fillable = [
        'employee_id',
        'type',
        'status',
        'message',
        'requested_date',
        'days',
        'end_date',
        'overtime_hours',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'end_date' => 'date',
        'type' => RequestType::class,
        'status' => RequestStatus::class,
    ];

    /**
     * --------------------------------------------------------------------------
     * Search Configuration
     * --------------------------------------------------------------------------
     */

    protected array $searchable = [
        'employee.first_name',
        'employee.last_name',
        'employee.position.name',
        'employee.position.department.name',
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

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * --------------------------------------------------------------------------
     * Scopes
     * --------------------------------------------------------------------------
     */

    public function scopeLeaves($query)
    {
        return $query->where('type', RequestType::LEAVE);
    }

    public function scopeOvertimes($query)
    {
        return $query->where('type', RequestType::OVERTIME);
    }

    public function scopePending($query)
    {
        return $query->where('status', RequestStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', RequestStatus::APPROVED);
    }
}
