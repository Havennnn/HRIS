<?php

namespace App\Enums\Type;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum HolidayType: int
{
    use HasMeta;
    use HasOptions;

    case REGULAR = 1;
    case SPECIAL = 2;

    protected static function metaMap(): array
    {
        return [
            self::REGULAR->value => [
                'label' => 'Regular Holiday', 
                'variant' => 'badge-regular'
            ],
            self::SPECIAL->value => [
                'label' => 'Special Holiday', 
                'variant' => 'badge-special'
            ],
        ];
    }
}
