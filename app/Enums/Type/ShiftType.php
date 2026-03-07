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
            self::MORNING->name => [
                'label' => 'Morning',
                'variant' => 'badge-info',
            ],
            self::AFTERNOON->name => [
                'label' => 'Afternoon',
                'variant' => 'badge-warning',
            ],
            self::NIGHT->name => [
                'label' => 'Night',
                'variant' => 'badge-secondary',
            ],
            self::FLEXIBLE->name => [
                'label' => 'Flexible',
                'variant' => 'badge-primary',
            ],
        ];
    }
}
