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
];
