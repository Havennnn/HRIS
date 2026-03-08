<?php

namespace App\Enums\Status;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum AttendanceStatus: int
{
    use HasMeta;
    use HasOptions;

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
            self::PRESENT->value => [
                'label' => 'Present',
                'variant' => 'badge-present',
            ],
            self::ABSENT->value => [
                'label' => 'Absent',
                'variant' => 'badge-absent',
            ],
            self::LATE->value => [
                'label' => 'Late',
                'variant' => 'badge-late',
            ],
            self::ON_TIME->value => [
                'label' => 'On Time',
                'variant' => 'badge-on-time',
            ],
            self::OVERTIME->value => [
                'label' => 'Overtime',
                'variant' => 'badge-overtime',
            ],
        ];
    }
}
