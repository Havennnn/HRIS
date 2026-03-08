<?php

namespace App\Enums\Status;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum EmployeeStatus: int
{
    use HasMeta;
    use HasOptions;

    case ACTIVE = 1;
    case INACTIVE = 2;
    case SICK = 3;
    case VACATION = 4;
    case ONBOARDING = 5;
    case TERMINATED = 6;

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::ACTIVE->value => [
                'label' => 'Active',
                'variant' => 'badge-active',
            ],
            self::INACTIVE->value => [
                'label' => 'Inactive',
                'variant' => 'badge-inactive',
            ],
            self::SICK->value => [
                'label' => 'Sick',
                'variant' => 'badge-sick',
            ],
            self::VACATION->value => [
                'label' => 'Vacation',
                'variant' => 'badge-vacation',
            ],
            self::ONBOARDING->value => [
                'label' => 'Onboarding',
                'variant' => 'badge-onboarding',
            ],
            self::TERMINATED->value => [
                'label' => 'Terminated',
                'variant' => 'badge-terminated',
            ],
        ];
    }
}
