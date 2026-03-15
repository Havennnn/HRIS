<?php

namespace App\Models;

use App\Enums\Status\PayrollStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PiaCore\Models\Concerns\HasActivityLogs;
use PiaCore\Search\SearchConfig;

class Payroll extends Model
{
    use HasFactory;
    use HasActivityLogs;
    use SearchConfig;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    protected $fillable = [
        'employee_id',
        'status',
        'basic_salary',
        'tax',
        'sss',
        'pagibig',
        'philhealth',
        'allowance',
        'gross_pay',
        'net_pay',
        'pay_period_start',
        'pay_period_end',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'status' => PayrollStatus::class,
        'tax' => 'decimal:2',
        'sss' => 'decimal:2',
        'pagibig' => 'decimal:2',
        'philhealth' => 'decimal:2',
        'allowance' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
    ];

    /**
     * --------------------------------------------------------------------------
     * Search Configuration
     * --------------------------------------------------------------------------
     */

    protected array $searchable = [
        'employee.first_name',
        'employee.last_name',
        'employee.middle_name',
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

    public function adjustments(): HasMany
    {
        return $this->hasMany(PayrollAdjustment::class);
    }
}
