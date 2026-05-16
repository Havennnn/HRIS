<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Shift Fallback
    |--------------------------------------------------------------------------
    |
    | Used by attendance API when an employee has no assigned shift/schedule.
    | Weekdays use ISO-8601 numbering where Monday=1 and Sunday=7.
    |
    */

    'default_shift' => [
        'enabled' => env('ATTENDANCE_DEFAULT_SHIFT_ENABLED', true),
        'weekdays' => [1, 2, 3, 4, 5],
        'start' => env('ATTENDANCE_DEFAULT_SHIFT_START', '08:00:00'),
        'end' => env('ATTENDANCE_DEFAULT_SHIFT_END', '16:00:00'),
    ],

];
