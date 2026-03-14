<?php

namespace App\Enums\Status;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum RequestStatus: int
{
    use HasMeta;
    use HasOptions;

    case PENDING = 1;
    case REVIEWING = 2;
    case APPROVED = 3;
    case REJECTED = 4;
    case CANCELLED = 5;
    case RESCHEDULE = 6;
    case COMPLETED = 7;
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
                'label' => 'Under Review',
                'variant' => 'badge-reviewing',
            ],
            self::APPROVED->value => [
                'label' => 'Approved',
                'variant' => 'badge-approved',
            ],
            self::REJECTED->value => [
                'label' => 'Rejected',
                'variant' => 'badge-rejected',
            ],
            self::CANCELLED->value => [
                'label' => 'Cancelled',
                'variant' => 'badge-cancelled',
            ],
            self::RESCHEDULE->value => [
                'label' => 'Reschedule',
                'variant' => 'badge-reschedule',
            ],
            self::COMPLETED->value => [
                'label' => 'Completed',
                'variant' => 'badge-completed',
            ],
        ];
    }
}
