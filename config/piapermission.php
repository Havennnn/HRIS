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
    ]
];
