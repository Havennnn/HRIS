<?php

namespace App\Constant;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;
use PiaCore\Support\AdminRoles;

enum AdminRole: string
{
    use HasMeta;
    use HasOptions;

    case SUPER_ADMIN = AdminRoles::SUPER_ADMIN;
    case HEAD_HR = 'head_hr';
    case HR_OFFICER = 'hr_officer';
    case FINANCE_MANAGER = 'finance_manager';
    case FINANCE_OFFICER = 'finance_officer';
    case PROJECT_MANAGER = 'project_manager';

    /**
     * Add app-specific roles below as needed.
     * Example: case IT_ADMIN = 'it_admin';
     */

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::SUPER_ADMIN->name => [
                'label' => 'Super Administrator',
                'variant' => 'badge-super-admin',
            ],
            self::HEAD_HR->name => [
                'label' => 'Head HR',
                'variant' => 'badge-head-hr',
            ],
            self::HR_OFFICER->name => [
                'label' => 'HR Officer',
                'variant' => 'badge-hr-officer',
            ],
            self::FINANCE_MANAGER->name => [
                'label' => 'Finance Manager',
                'variant' => 'badge-finance-manager',
            ],
            self::FINANCE_OFFICER->name => [
                'label' => 'Finance Officer',
                'variant' => 'badge-finance-officer',
            ],
            self::PROJECT_MANAGER->name => [
                'label' => 'Project Manager',
                'variant' => 'badge-project-manager',
            ],
        ];
    }
}
