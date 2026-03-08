<?php

namespace App\Enums\Type;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum EmployeeContactType: int
{
    use HasMeta;
    use HasOptions;

    case RELATIVE = 1;
    case PARENT = 2;
    case OTHERS = 3;

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::RELATIVE->value => [
                'label' => 'Relative',
            ],
            self::PARENT->value => [
                'label' => 'Parent',
            ],
            self::OTHERS->value => [
                'label' => 'Others',
            ],
        ];
    }
}
