<?php

namespace App\Enums\Status;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum ApplicationStatus: int
{
    use HasMeta;
    use HasOptions;

    case PENDING = 1;
    case REVIEWING = 2;
    case INTERVIEW = 3;
    case REJECTED = 4;
    case HIRED = 5;

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::PENDING->value => [
                'label' => 'Pending',
                'variant' => 'badge-pending',
            ],
            self::REVIEWING->value => [
                'label' => 'Reviewing',
                'variant' => 'badge-reviewing',
            ],
            self::INTERVIEW->value => [
                'label' => 'Interview',
                'variant' => 'badge-interview',
            ],
            self::REJECTED->value => [
                'label' => 'Rejected',
                'variant' => 'badge-rejected',
            ],
            self::HIRED->value => [
                'label' => 'Hired',
                'variant' => 'badge-hired',
            ],
        ];
    }
}
