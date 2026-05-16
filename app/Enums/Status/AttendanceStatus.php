<?php

namespace App\Enums\Status;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum AttendanceStatus: int
{
    use HasMeta;
    use HasOptions;

    case PRESENT = 1;
    case LATE = 2;
    case EARLY_LEAVE = 3;
    case OVERTIME = 4;
    case ABSENT = 5;

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
            self::LATE->value => [
                'label' => 'Late',
                'variant' => 'badge-late',
            ],
            self::EARLY_LEAVE->value => [
                'label' => 'Early Leave',
                'variant' => 'badge-early-leave',
            ],
            self::OVERTIME->value => [
                'label' => 'Overtime',
                'variant' => 'badge-overtime',
            ],
            self::ABSENT->value => [
                'label' => 'Absent',
                'variant' => 'badge-destructive',
            ],
        ];
    }
}
