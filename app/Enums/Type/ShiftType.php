<?php

namespace App\Enums\Type;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum ShiftType: int
{
    use HasMeta;
    use HasOptions;

    case MORNING = 1;
    case AFTERNOON = 2;
    case NIGHT = 3;
    case FLEXIBLE = 4;

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::MORNING->value => [
                'label' => 'Morning',
            ],
            self::AFTERNOON->value => [
                'label' => 'Afternoon',
            ],
            self::NIGHT->value => [
                'label' => 'Night',
            ],
            self::FLEXIBLE->value => [
                'label' => 'Flexible',
            ],
        ];
    }
}
