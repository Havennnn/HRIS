<?php

namespace App\Constant;

class PayoutConfigurationDefaults
{
    public const FIRST_HALF = [
        'name' => 'First Half',
        'slug' => 'first-half',
        'period_start_day' => 1,
        'period_end_day' => 15,
        'period_end_is_last_day' => false,
        'cutoff_generation_day' => 10,
        'cutoff_disburse_day' => 15,
        'disburse_is_last_day' => false,
        'assumed_from_day' => 11,
        'is_active' => true,
    ];

    public const SECOND_HALF = [
        'name' => 'Second Half',
        'slug' => 'second-half',
        'period_start_day' => 16,
        'period_end_day' => null,
        'period_end_is_last_day' => true,
        'cutoff_generation_day' => 25,
        'cutoff_disburse_day' => null,
        'disburse_is_last_day' => true,
        'assumed_from_day' => 26,
        'is_active' => true,
    ];

    public static function all(): array
    {
        return [self::FIRST_HALF, self::SECOND_HALF];
    }
}
