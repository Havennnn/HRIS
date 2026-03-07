<?php

namespace App\Enums\Status;

use PiaCore\Enums\Concerns\HasMeta;

enum LeaveRequestStatus: int
{
    use HasMeta;

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
            self::PENDING->name => [
                'label' => 'Pending',
                'variant' => 'badge-warning',
            ],
            self::APPROVED->name => [
                'label' => 'Approved',
                'variant' => 'badge-success',
            ],
            self::REJECTED->name => [
                'label' => 'Rejected',
                'variant' => 'badge-danger',
            ],
            self::CANCELLED->name => [
                'label' => 'Cancelled',
                'variant' => 'badge-secondary',
            ],
        ];
    }
}
