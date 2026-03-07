<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PiaCore\Models\Concerns\HasActivityLogs;

class Payroll extends Model
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
        'basic_salary',
        'tax',
        'sss',
        'allowance',
        'net_pay',
        'pay_period_start',
        'pay_period_end',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'basic_salary' => 'decimal:2',
        'tax' => 'decimal:2',
        'sss' => 'decimal:2',
        'allowance' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
    ];

    /**
     * Get the employee that owns the payroll.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the payroll adjustments for this payroll.
     */
    public function adjustments(): HasMany
    {
        return $this->hasMany(PayrollAdjustment::class);
    }
}
