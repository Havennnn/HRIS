<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Channels
    |--------------------------------------------------------------------------
    |
    | Fallback when a notification route doesn't specify channels.
    |
    */

    'default' => ['database'],

    /*
    |--------------------------------------------------------------------------
    | Notification Routes
    |--------------------------------------------------------------------------
    |
    */

    'routes' => [

        'import_completed' => [
            'label'    => 'Import Completed',
            'channels' => ['database'],
            'tags'     => ['success_count', 'error_count'],
            'stubs'    => [
                'database' => null,
            ],
        ],

        'new_request_submitted' => [
            'label'    => 'New Request Submitted',
            'channels' => ['database', 'mail'],
            'tags'     => ['employee_name', 'request_type', 'date', 'message'],
            'stubs'    => [
                'database' => null,
                'mail'     => null,
            ],
        ],

        'payroll_generated' => [
            'label'    => 'Payroll Generated',
            'channels' => ['database', 'mail'],
            'tags'     => ['employee_name', 'gross_pay', 'net_pay', 'period'],
            'stubs'    => [
                'database' => null,
                'mail'     => null,
            ],
        ],

        'payroll_failed' => [
            'label'    => 'Payroll Generation Failed',
            'channels' => ['database', 'mail'],
            'tags'     => ['period_start', 'period_end', 'error_message'],
            'stubs'    => [
                'database' => null,
                'mail'     => null,
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    */

    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
        'from'   => env('SMS_FROM', 'HRIS'),

        'twilio' => [
            'sid'   => env('TWILIO_SID'),
            'token' => env('TWILIO_TOKEN'),
            'from'  => env('TWILIO_FROM'),
        ],

        'vonage' => [
            'key'    => env('VONAGE_KEY'),
            'secret' => env('VONAGE_SECRET'),
            'from'   => env('VONAGE_FROM'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mail Configuration
    |--------------------------------------------------------------------------
    */

    'mail' => [
        'from' => [
            'address' => env('NOTIFICATION_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'noreply@hris.local')),
            'name'    => env('NOTIFICATION_MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'HRIS')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Notification Recipients
    |--------------------------------------------------------------------------
    */

    'admin_recipients' => explode(',', env('NOTIFICATION_ADMIN_EMAILS', '')),
];
