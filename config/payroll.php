<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Working Days Per Month
    |--------------------------------------------------------------------------
    |
    | The standard number of working days used to calculate the daily rate
    | from an employee's monthly basic salary.
    |
    */

    'working_days_per_month' => env('PAYROLL_WORKING_DAYS_PER_MONTH', 22),

    /*
    |--------------------------------------------------------------------------
    | Work Hours Per Day
    |--------------------------------------------------------------------------
    |
    | Used for converting daily rate into hourly rate for overtime computation.
    |
    */

    'work_hours_per_day' => env('PAYROLL_WORK_HOURS_PER_DAY', 8),

    /*
    |--------------------------------------------------------------------------
    | Monetary Precision
    |--------------------------------------------------------------------------
    |
    | Decimal scale used for rounding payroll monetary values.
    |
    */

    'money_precision' => env('PAYROLL_MONEY_PRECISION', 2),

    /*
    |--------------------------------------------------------------------------
    | Overtime Rate Multiplier
    |--------------------------------------------------------------------------
    |
    | Multiplier applied to the hourly rate when computing overtime pay.
    | 1.25 means 125% of the regular hourly rate.
    |
    */

    'hourly_rate_multiplier' => env('PAYROLL_HOURLY_RATE_MULTIPLIER', 1.25),

    /*
    |--------------------------------------------------------------------------
    | Late Deduction Per Minute
    |--------------------------------------------------------------------------
    |
    | Amount deducted (in currency units) for every minute of tardiness.
    |
    */

    'late_deduction_per_minute' => env('PAYROLL_LATE_DEDUCTION_PER_MINUTE', 0.50),

    /*
    |--------------------------------------------------------------------------
    | Philippine Withholding Tax (Monthly)
    |--------------------------------------------------------------------------
    |
    | BIR TRAIN monthly withholding tax table.
    | Each bracket entry:
    |   'from' => lower bound of monthly taxable compensation (inclusive)
    |   'to'   => upper bound (exclusive, null = open-ended)
    |   'base' => fixed monthly withholding at bracket start
    |   'rate' => marginal rate applied to excess over 'from'
    |
    */

    'pay_periods_per_month' => env('PAYROLL_PAY_PERIODS_PER_MONTH', 2),

    /*
    |--------------------------------------------------------------------------
    | Holiday Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for handling holidays in payroll calculations.
    |
    | 'exclude_holidays_from_expected' - When true, holidays are excluded from
    |     expected working days calculation (reduces expected attendance).
    |
    | 'regular_holiday_multiplier' - Multiplier for daily rate when working on
    |     regular holidays (e.g., 1.0 = 100% extra, 2.0 = double pay).
    |
    | 'special_holiday_multiplier' - Multiplier for daily rate when working on
    |     special holidays (e.g., 1.0 = 100% extra, 0.5 = 50% extra).
    |
    */

    'holidays' => [
        'exclude_holidays_from_expected' => env('PAYROLL_EXCLUDE_HOLIDAYS_FROM_EXPECTED', true),
        'regular_holiday_multiplier' => env('PAYROLL_REGULAR_HOLIDAY_MULTIPLIER', 1.5),
        'special_holiday_multiplier' => env('PAYROLL_SPECIAL_HOLIDAY_MULTIPLIER', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contribution Proration
    |--------------------------------------------------------------------------
    |
    | When true, monthly SSS/Pag-IBIG/PhilHealth employee shares are divided
    | across payroll periods (e.g. semi-monthly = 2).
    |
    */

    'contributions' => [
        'prorate_per_payroll' => env('PAYROLL_PRORATE_CONTRIBUTIONS', true),
    ],

    'bir_withholding_monthly' => [
        ['from' => 0, 'to' => 20833, 'base' => 0, 'rate' => 0.00],
        ['from' => 20833, 'to' => 33333, 'base' => 0, 'rate' => 0.15],
        ['from' => 33333, 'to' => 66667, 'base' => 1875, 'rate' => 0.20],
        ['from' => 66667, 'to' => 166667, 'base' => 8541.8, 'rate' => 0.25],
        ['from' => 166667, 'to' => 666667, 'base' => 33541.8, 'rate' => 0.30],
        ['from' => 666667, 'to' => null, 'base' => 183541.8, 'rate' => 0.35],
    ],

    /*
    |--------------------------------------------------------------------------
    | SSS Contribution Table
    |--------------------------------------------------------------------------
    |
    | Simplified SSS monthly contribution lookup by salary range.
    | Each entry:
    |   'from'         => minimum salary (inclusive)
    |   'to'           => maximum salary (exclusive, null = no upper limit)
    |   'contribution' => fixed SSS contribution amount
    |
    */

    'sss_table' => [
        ['from' => 0, 'to' => 5000, 'contribution' => 200],
        ['from' => 5000, 'to' => 7500, 'contribution' => 350],
        ['from' => 7500, 'to' => 10000, 'contribution' => 500],
        ['from' => 10000, 'to' => 12500, 'contribution' => 650],
        ['from' => 12500, 'to' => 15000, 'contribution' => 800],
        ['from' => 15000, 'to' => 17500, 'contribution' => 950],
        ['from' => 17500, 'to' => 20000, 'contribution' => 1100],
        ['from' => 20000, 'to' => 25000, 'contribution' => 1250],
        ['from' => 25000, 'to' => null, 'contribution' => 1350],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pag-IBIG Contribution
    |--------------------------------------------------------------------------
    |
    | Simplified employee share configuration.
    |
    */

    'pagibig' => [
        'threshold_salary' => env('PAYROLL_PAGIBIG_THRESHOLD_SALARY', 1500),
        'rate_below_threshold' => env('PAYROLL_PAGIBIG_RATE_BELOW_THRESHOLD', 0.01),
        'rate_at_or_above_threshold' => env('PAYROLL_PAGIBIG_RATE_AT_OR_ABOVE_THRESHOLD', 0.02),
        'max_salary_base' => env('PAYROLL_PAGIBIG_MAX_SALARY_BASE', 5000),
    ],

    /*
    |--------------------------------------------------------------------------
    | PhilHealth Contribution
    |--------------------------------------------------------------------------
    |
    | Simplified employee share configuration.
    |
    */

    'philhealth' => [
        'premium_rate' => env('PAYROLL_PHILHEALTH_PREMIUM_RATE', 0.05),
        'employee_share' => env('PAYROLL_PHILHEALTH_EMPLOYEE_SHARE', 0.50),
        'min_salary_base' => env('PAYROLL_PHILHEALTH_MIN_SALARY_BASE', 10000),
        'max_salary_base' => env('PAYROLL_PHILHEALTH_MAX_SALARY_BASE', 100000),
    ],

];
