<?php

namespace App\Enums\Status;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum PayrollStatus: int
{
    use HasMeta;
    use HasOptions;

    case PENDING = 1;
    case APPROVED = 2;
    case DISBURSED = 3;
    case REJECTED = 4;

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
            self::DISBURSED->value => [
                'label' => 'Disbursed',
                'variant' => 'badge-disbursed',
            ],
            self::REJECTED->value => [
                'label' => 'Rejected',
                'variant' => 'badge-rejected',
            ],
        ];
    }
}
