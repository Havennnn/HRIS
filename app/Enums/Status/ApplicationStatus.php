<?php

namespace App\Enums\Status;

use PiaCore\Enums\Concerns\HasMeta;

enum ApplicationStatus: int
{
    use HasMeta;

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
            self::PENDING->name => [
                'label' => 'Pending',
                'variant' => 'badge-warning',
            ],
            self::REVIEWING->name => [
                'label' => 'Reviewing',
                'variant' => 'badge-info',
            ],
            self::INTERVIEW->name => [
                'label' => 'Interview',
                'variant' => 'badge-primary',
            ],
            self::REJECTED->name => [
                'label' => 'Rejected',
                'variant' => 'badge-danger',
            ],
            self::HIRED->name => [
                'label' => 'Hired',
                'variant' => 'badge-success',
            ],
        ];
    }
}
