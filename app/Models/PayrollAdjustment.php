<?php

namespace App\Models;

use App\Enums\Type\PayrollAdjustmentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollAdjustment extends Model
{
    use HasFactory;

    /**
     * --------------------------------------------------------------------------
     * Attributes
     * --------------------------------------------------------------------------
     */
    protected $fillable = [
        'payroll_id',
        'type',
        'reason',
        'amount',
    ];

    protected $casts = [
        'type' => PayrollAdjustmentType::class,
        'amount' => 'decimal:2',
    ];

    /**
     * --------------------------------------------------------------------------
     * Relationships
     * --------------------------------------------------------------------------
     */
    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }
}
