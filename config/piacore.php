<?php

return [
    'route_prefix' => 'admin',

    'inertia_prefix' => 'Admin',

    'admin_ui' => [
        'enabled' => true,
        'dashboard_path' => 'dashboard',
        'dashboard_name' => 'dashboard',
        'dashboard_component' => 'Dashboard',
        'dashboard_middleware' => ['auth:admin'],
        'auth' => [
            'login_component' => 'Auth/Login',
            'forgot_password_component' => 'Auth/ForgotPassword',
            'reset_password_component' => 'Auth/ResetPassword',
            'verify_email_component' => 'Auth/VerifyEmail',
            'confirm_password_component' => 'Auth/ConfirmPassword',
            'two_factor_component' => 'Auth/TwoFactorChallenge',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | UI / Render Configuration
    |--------------------------------------------------------------------------
    |
    | Configure global layout and authentication page components here. These
    | options are read by the UIManager and are available under the `ui`
    | key in this file. Set values to `null` to use the package defaults.
    |
    */
    'ui' => [
        // Global layout component that wraps all admin pages (null = no custom layout)
        'layout_component' => null,

        // Sidebar/navigation component
        'sidebar_component' => null,

        // Top navigation bar component
        'topbar_component' => null,

        // Authentication layout and pages (have sensible defaults)
        'auth_layout' => 'Auth/AuthLayout',
        'login' => 'auth/Login',
        'dashboard' => 'Dashboard',
    ],

    'model_services' => [
        // App\Models\Product::class => App\Services\ProductService::class,
    ],

    'actions' => [
        // PiaCore\Actions\Resource\ListAction::class => App\Actions\Product\CustomListAction::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how search functionality works across the admin panel.
    | PiaCore supports three states:
    |
    | 1. No Scout installed: Uses traditional LIKE %search% queries
    | 2. Scout with database driver: Uses Scout's database engine
    | 3. Scout with Meilisearch: Uses Scout's Meilisearch engine
    |
    | The 'driver' option can be:
    | - 'auto' (default): Automatically detect based on environment
    | - 'like': Force use of LIKE queries (no Scout required)
    | - 'scout': Force use of Laravel Scout (requires Scout installed)
    |
    */
    'search' => [
        'enabled' => true,

        // Driver to use: 'auto', 'like', or 'scout'
        // 'auto' will detect Scout and use it if available, otherwise fall back to 'like'
        'driver' => 'auto',

        // Query parameter name for search
        'param' => 'search',

        // Minimum characters required for search
        'min_length' => 2,

        // Meilisearch-specific configuration
        // Only used when Scout is configured with meilisearch driver
        'meilisearch' => [
            'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
            'key' => env('MEILISEARCH_KEY'),
        ],
    ],
];
