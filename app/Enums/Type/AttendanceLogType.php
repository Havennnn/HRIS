<?php

namespace App\Enums\Type;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum AttendanceLogType: int
{
    use HasMeta;
    use HasOptions;

    case IN = 1;
    case OUT = 2;

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::IN->value => [
                'label' => 'Clock In',
                'variant' => 'badge-success',
            ],
            self::OUT->value => [
                'label' => 'Clock Out',
                'variant' => 'badge-warning',
            ],
        ];
    }
}
