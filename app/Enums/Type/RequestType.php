<?php

namespace App\Enums\Type;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum RequestType: int
{
    use HasMeta;
    use HasOptions;

    case LEAVE = 1;
    case OVERTIME = 2;
    case WORK_ON_HOLIDAY = 3;

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::LEAVE->value => [
                'label' => 'Leave',
            ],
            self::OVERTIME->value => [
                'label' => 'Overtime',
            ],
            self::WORK_ON_HOLIDAY->value => [
                'label' => 'Work on Holiday',
            ],
        ];
    }
}
