<?php

namespace App\Enums\Status;

use PiaCore\Enums\Concerns\HasMeta;

enum AttendanceStatus: int
{
    use HasMeta;

    case PRESENT = 1;
    case ABSENT = 2;
    case LATE = 3;
    case ON_TIME = 4;
    case OVERTIME = 5;

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::PRESENT->name => [
                'label' => 'Present',
                'variant' => 'badge-success',
            ],
            self::ABSENT->name => [
                'label' => 'Absent',
                'variant' => 'badge-danger',
            ],
            self::LATE->name => [
                'label' => 'Late',
                'variant' => 'badge-warning',
            ],
            self::ON_TIME->name => [
                'label' => 'On Time',
                'variant' => 'badge-info',
            ],
            self::OVERTIME->name => [
                'label' => 'Overtime',
                'variant' => 'badge-primary',
            ],
        ];
    }
}
