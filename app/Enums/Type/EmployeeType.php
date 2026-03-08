<?php

namespace App\Enums\Type;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum EmployeeType: int
{
    use HasMeta;
    use HasOptions;

    case TRAINEE = 1;
    case REGULAR = 2;
    case PROBATIONARY = 3;

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::TRAINEE->value => [
                'label' => 'Trainee',
            ],
            self::REGULAR->value => [
                'label' => 'Regular',
            ],
            self::PROBATIONARY->value => [
                'label' => 'Probationary',
            ],
        ];
    }
}
