<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use PiaCore\Models\Concerns\HasActivityLogs;

class PayoutConfiguration extends Model
{
    use HasActivityLogs;

    protected $fillable = [
        'name',
        'slug',
        'period_start_day',
        'period_end_day',
        'period_end_is_last_day',
        'cutoff_generation_day',
        'cutoff_disburse_day',
        'disburse_is_last_day',
        'assumed_from_day',
        'is_active',
    ];

    protected $casts = [
        'period_end_is_last_day' => 'boolean',
        'disburse_is_last_day' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function resolvePeriodStart(\DateTimeInterface $reference): CarbonImmutable
    {
        $ref = CarbonImmutable::parse($reference)->startOfDay();

        return $ref->setDay($this->period_start_day);
    }

    public function resolvePeriodEnd(\DateTimeInterface $reference): CarbonImmutable
    {
        $ref = CarbonImmutable::parse($reference)->startOfDay();

        if ($this->period_end_is_last_day) {
            return $ref->endOfMonth()->startOfDay();
        }

        return $ref->setDay($this->period_end_day);
    }

    public function resolveGenerationDate(\DateTimeInterface $reference): CarbonImmutable
    {
        return CarbonImmutable::parse($reference)->startOfDay()->setDay($this->cutoff_generation_day);
    }

    public function isGenerationDay(\DateTimeInterface $date): bool
    {
        return CarbonImmutable::parse($date)->day === $this->cutoff_generation_day;
    }

    public function isDisburseDay(\DateTimeInterface $date): bool
    {
        $date = CarbonImmutable::parse($date)->startOfDay();

        if ($this->disburse_is_last_day) {
            return $date->isLastOfMonth();
        }

        return $date->day === $this->cutoff_disburse_day;
    }

    public function isSecondHalf(): bool
    {
        return $this->period_end_is_last_day;
    }
}
