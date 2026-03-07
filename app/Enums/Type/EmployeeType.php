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
            self::TRAINEE->name => [
                'label' => 'Trainee',
            ],
            self::REGULAR->name => [
                'label' => 'Regular',
            ],
            self::PROBATIONARY->name => [
                'label' => 'Probationary',
            ],
        ];
    }
}
