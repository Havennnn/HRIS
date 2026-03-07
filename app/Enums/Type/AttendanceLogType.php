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
            self::IN->name => [
                'label' => 'Clock In',
                'variant' => 'badge-success',
            ],
            self::OUT->name => [
                'label' => 'Clock Out',
                'variant' => 'badge-warning',
            ],
        ];
    }
}
