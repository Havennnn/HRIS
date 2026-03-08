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
    case OVERTIME = 4;

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
            self::OVERTIME->value => [
                'label' => 'Overtime',
                'variant' => 'badge-overtime',
            ],
        ];
    }
}
