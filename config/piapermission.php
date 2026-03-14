<?php

use PiaCore\Enums\AdminRole;

/*
|--------------------------------------------------------------------------
| PiaCore Permissions Configuration
|--------------------------------------------------------------------------
|
| Define all permissions for the admin panel here. Each permission key
| should map to an array containing 'label' (human-readable name) and
| 'roles' (array of AdminRole enum values that have this permission).
|
| The permission keys follow the pattern: can-{action}-{resource}
| Available actions: list, view, create, update, delete, restore
|
| The guard_name for each permission will be automatically set to match
| the role value from AdminRole enum (e.g., 'super_admin').
|
*/

return [
    // Admin Management Permissions
    'can-list-admins' => [
        'label' => 'List Admins',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-create-admin' => [
        'label' => 'Create Admin',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-update-admin' => [
        'label' => 'Update Admin',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-archive-admin' => [
        'label' => 'Archive Admin',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-restore-admin' => [
        'label' => 'Restore Admin',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],

    // Department Management Permissions
    'can-list-departments' => [
        'label' => 'List Departments',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-create-department' => [
        'label' => 'Create Department',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-update-department' => [
        'label' => 'Update Department',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-archive-department' => [
        'label' => 'Archive Department',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-restore-department' => [
        'label' => 'Restore Department',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],

    // Position Management Permissions
    'can-list-positions' => [
        'label' => 'List Positions',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-create-position' => [
        'label' => 'Create Position',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-update-position' => [
        'label' => 'Update Position',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-archive-position' => [
        'label' => 'Archive Position',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-restore-position' => [
        'label' => 'Restore Position',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],

    // Employee Management Permissions
    'can-list-employees' => [
        'label' => 'List Employees',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-create-employee' => [
        'label' => 'Create Employee',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-update-employee' => [
        'label' => 'Update Employee',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-archive-employee' => [
        'label' => 'Archive Employee',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-restore-employee' => [
        'label' => 'Restore Employee',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],

    // Employee Attendance Permissions
    'can-list-attendances' => [
        'label' => 'List Attendances',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],

    // Employees Attendance Logs Permission
    'can-list-attendance-logs' => [
        'label' => 'List Attendances Logs',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],

    // Request Management Permissions
    'can-list-requests' => [
        'label' => 'List Requests',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-view-request' => [
        'label' => 'View Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-approve-request' => [
        'label' => 'Approve Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-reject-request' => [
        'label' => 'Reject Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-cancel-request' => [
        'label' => 'Cancel Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-complete-request' => [
        'label' => 'Complete Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-archive-request' => [
        'label' => 'Archive Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-restore-request' => [
        'label' => 'Restore Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],

    // Payroll Management Permissions
    'can-list-payrolls' => [
        'label' => 'List Payrolls',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-approve-payroll' => [
        'label' => 'Approve Payroll',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-reject-payroll' => [
        'label' => 'Reject Payroll',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-disburse-payroll' => [
        'label' => 'Disburse Payroll',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],

    // Holiday Management Permissions
    'can-list-holidays' => [
        'label' => 'List Holidays',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-create-holiday' => [
        'label' => 'Create Holiday',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-update-holiday' => [
        'label' => 'Update Holiday',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-archive-holiday' => [
        'label' => 'Archive Holiday',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
    'can-restore-holiday' => [
        'label' => 'Restore Holiday',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],
];
