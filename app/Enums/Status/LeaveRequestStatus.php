<?php

namespace App\Enums\Status;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum LeaveRequestStatus: int
{
    use HasMeta;
    use HasOptions;

    case PENDING = 1;
    case APPROVED = 2;
    case REJECTED = 3;
    case CANCELLED = 4;

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
        ];
    }
}
