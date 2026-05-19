<?php

use App\Constant\AdminRole;

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
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
            AdminRole::FINANCE_MANAGER,
            AdminRole::FINANCE_OFFICER,
            AdminRole::PROJECT_MANAGER,
        ],
    ],
    'can-create-department' => [
        'label' => 'Create Department',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-update-department' => [
        'label' => 'Update Department',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-archive-department' => [
        'label' => 'Archive Department',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-restore-department' => [
        'label' => 'Restore Department',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],

    // Position Management Permissions
    'can-list-positions' => [
        'label' => 'List Positions',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
            AdminRole::FINANCE_MANAGER,
            AdminRole::FINANCE_OFFICER,
            AdminRole::PROJECT_MANAGER,
        ],
    ],
    'can-create-position' => [
        'label' => 'Create Position',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-update-position' => [
        'label' => 'Update Position',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
            AdminRole::FINANCE_MANAGER,
        ],
    ],
    'can-archive-position' => [
        'label' => 'Archive Position',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-restore-position' => [
        'label' => 'Restore Position',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],

    // Employee Management Permissions
    'can-list-employees' => [
        'label' => 'List Employees',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
            AdminRole::FINANCE_MANAGER,
            AdminRole::FINANCE_OFFICER,
            AdminRole::PROJECT_MANAGER,
        ],
    ],
    'can-create-employee' => [
        'label' => 'Create Employee',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-edit-employee' => [
        'label' => 'Update Employee',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
            AdminRole::FINANCE_MANAGER,
            AdminRole::FINANCE_OFFICER,
        ],
    ],
    'can-update-employee' => [
        'label' => 'Update Employee',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-archive-employee' => [
        'label' => 'Archive Employee',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-restore-employee' => [
        'label' => 'Restore Employee',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],

    // Employee Attendance Permissions
    'can-list-attendances' => [
        'label' => 'List Attendances',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
            AdminRole::FINANCE_MANAGER,
            AdminRole::FINANCE_OFFICER,
        ],
    ],

    // Employees Attendance Logs Permission
    'can-list-attendance-logs' => [
        'label' => 'List Attendances Logs',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
            AdminRole::FINANCE_MANAGER,
            AdminRole::FINANCE_OFFICER,
        ],
    ],

    // Request Management Permissions
    'can-list-requests' => [
        'label' => 'List Requests',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-view-request' => [
        'label' => 'View Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-approve-request' => [
        'label' => 'Approve Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-reject-request' => [
        'label' => 'Reject Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-cancel-request' => [
        'label' => 'Cancel Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-complete-request' => [
        'label' => 'Complete Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-archive-request' => [
        'label' => 'Archive Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-restore-request' => [
        'label' => 'Restore Request',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],

    // Payroll Management Permissions
    'can-list-payrolls' => [
        'label' => 'List Payrolls',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::FINANCE_MANAGER,
            AdminRole::FINANCE_OFFICER,
        ],
    ],
    'can-approve-payroll' => [
        'label' => 'Approve Payroll',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::FINANCE_MANAGER,
        ],
    ],
    'can-reject-payroll' => [
        'label' => 'Reject Payroll',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::FINANCE_MANAGER,
        ],
    ],
    'can-disburse-payroll' => [
        'label' => 'Disburse Payroll',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::FINANCE_MANAGER,
            AdminRole::FINANCE_OFFICER,
        ],
    ],

    // Holiday Management Permissions
    'can-list-holidays' => [
        'label' => 'List Holidays',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-create-holiday' => [
        'label' => 'Create Holiday',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-update-holiday' => [
        'label' => 'Update Holiday',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-archive-holiday' => [
        'label' => 'Archive Holiday',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-restore-holiday' => [
        'label' => 'Restore Holiday',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],

    // Career Management Permissions
    'can-list-careers' => [
        'label' => 'List Careers',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-create-career' => [
        'label' => 'Create Career',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-update-career' => [
        'label' => 'Update Career',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-archive-career' => [
        'label' => 'Archive Career',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-restore-career' => [
        'label' => 'Restore Career',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],

    // Application Management Permissions
    'can-list-applications' => [
        'label' => 'List Applications',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-view-application' => [
        'label' => 'View Application',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-interview-application' => [
        'label' => 'Move Application to Interview',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-reject-application' => [
        'label' => 'Reject Application',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-hire-application' => [
        'label' => 'Hire Application',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-archive-application' => [
        'label' => 'Archive Application',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-restore-application' => [
        'label' => 'Restore Application',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],

    // KPI Management Permissions
    'can-list-kpis' => [
        'label' => 'List KPIs',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-create-kpi' => [
        'label' => 'Create KPI',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-update-kpi' => [
        'label' => 'Update KPI',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-archive-kpi' => [
        'label' => 'Archive KPI',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-restore-kpi' => [
        'label' => 'Restore KPI',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],

    // Settings - Payout Configuration Permissions
    'can-list-payout-configurations' => [
        'label' => 'List Payout Configurations',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::FINANCE_MANAGER,
        ],
    ],
    'can-update-payout-configuration' => [
        'label' => 'Update Payout Configuration',
        'roles' => [
            AdminRole::SUPER_ADMIN,
        ],
    ],

    // Performance Review Management Permissions
    'can-list-performance-reviews' => [
        'label' => 'List Performance Reviews',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
            AdminRole::HR_OFFICER,
        ],
    ],
    'can-create-performance-review' => [
        'label' => 'Create Performance Review',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-update-performance-review' => [
        'label' => 'Update Performance Review',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-archive-performance-review' => [
        'label' => 'Archive Performance Review',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
    'can-restore-performance-review' => [
        'label' => 'Restore Performance Review',
        'roles' => [
            AdminRole::SUPER_ADMIN,
            AdminRole::HEAD_HR,
        ],
    ],
];
