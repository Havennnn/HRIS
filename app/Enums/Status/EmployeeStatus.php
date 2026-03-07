<?php

namespace App\Enums\Status;

use PiaCore\Enums\Concerns\HasMeta;

enum EmployeeStatus: int
{
    use HasMeta;

    case ACTIVE = 1;
    case INACTIVE = 2;
    case SICK = 3;
    case VACATION = 4;
    case TERMINATED = 5;

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::ACTIVE->name => [
                'label' => 'Active',
                'variant' => 'badge-active',
            ],
            self::INACTIVE->name => [
                'label' => 'Inactive',
                'variant' => 'badge-inactive',
            ],
            self::SICK->name => [
                'label' => 'Sick',
                'variant' => 'badge-sick',
            ],
            self::VACATION->name => [
                'label' => 'Vacation',
                'variant' => 'badge-vacation',
            ],
            self::TERMINATED->name => [
                'label' => 'Terminated',
                'variant' => 'badge-terminated',
            ],
        ];
    }
}
