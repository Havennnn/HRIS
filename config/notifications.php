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
    | Define your notification types here. Run `php artisan piacore:sync-notification`
    | to publish editable stubs for each notification.
    |
    | Each route:
    |   channels      - ['database'], ['database', 'mail'], ['sms'], etc.
    |   tags          - Smart tags available: {{name}}, {{link}}, etc.
    |   mail_template - Custom Blade layout for email design (null = package default)
    |   stubs         - Content templates per channel
    |
    */

    'routes' => [

        'import_completed' => [
            'label'         => 'Import Success!',
            'channels'      => ['database', 'mail'],
            'tags'          => ['subject', 'body', 'first_name', 'last_name', 'import_count', 'received_at', 'url'],
            'mail_template' => null,
            'stubs'         => [
                'database' => 'database/import_completed',
                'mail'     => 'mail/import_completed',
            ],
        ],

        'import_failed' => [
            'label'         => 'Import Failed',
            'channels'      => ['database', 'mail'],
            'tags'          => ['subject', 'body', 'first_name', 'last_name', 'import_count', 'error_count', 'total_rows', 'received_at', 'url'],
            'mail_template' => null,
            'stubs'         => [
                'database' => 'database/import_failed',
                'mail'     => 'mail/import_failed',
            ],
        ],

        'new_request_submitted' => [
            'label'         => 'New Request Submitted',
            'channels'      => ['database', 'mail'],
            'tags'          => ['employee_name', 'request_type', 'date', 'message'],
            'mail_template' => null,
            'stubs'         => [
                'database' => 'database/new_request_submitted',
                'mail'     => 'mail/new_request_submitted',
            ],
        ],

        'payroll_generated' => [
            'label'         => 'Payroll Generated',
            'channels'      => ['database', 'mail'],
            'tags'          => ['employee_name', 'gross_pay', 'net_pay', 'period'],
            'mail_template' => null,
            'stubs'         => [
                'database' => 'database/payroll_generated',
                'mail'     => 'mail/payroll_generated',
            ],
        ],

        'payroll_failed' => [
            'label'         => 'Payroll Generation Failed',
            'channels'      => ['database', 'mail'],
            'tags'          => ['period_start', 'period_end', 'error_message'],
            'mail_template' => null,
            'stubs'         => [
                'database' => 'database/payroll_failed',
                'mail'     => 'mail/payroll_failed',
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

];
