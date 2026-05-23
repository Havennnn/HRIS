<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    |
    | Define filenames and MIME types per entity. These override the defaults
    | set in ManifestStructure and ExportStructure implementations.
    |
    | The config key is the snake_case of the structure class basename without
    | 'Manifest'/'Export' suffix.
    |
    | Example:
    |   EmployeeManifest  → key: 'employee'
    |   PayrollExport     → key: 'payroll'
    |
    */

    'manifests' => [
        'employee' => [
            'filename'  => 'employee-import-template.csv',
            'mime_type' => 'text/csv; charset=utf-8',
        ],
    ],

    'exports' => [
        'employee' => [
            'filename'  => 'employees.csv',
            'mime_type' => 'text/csv; charset=utf-8',
        ],
        'payroll' => [
            'filename'  => 'payroll-export.csv',
            'mime_type' => 'text/csv; charset=utf-8',
        ],
        'attendance_log' => [
            'filename'  => 'attendance-logs.csv',
            'mime_type' => 'text/csv; charset=utf-8',
        ],
    ],

];
