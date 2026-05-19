<?php

namespace App\Enums\Status;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum PerformanceReviewStatus: int
{
    use HasMeta;
    use HasOptions;

    case PENDING = 1;
    case IN_REVIEW = 2;
    case COMPLETED = 3;

    protected static function metaMap(): array
    {
        return [
            self::PENDING->value => [
                'label' => 'Pending',
                'variant' => 'badge-pending',
            ],
            self::IN_REVIEW->value => [
                'label' => 'In Review',
                'variant' => 'badge-reviewing',
            ],
            self::COMPLETED->value => [
                'label' => 'Completed',
                'variant' => 'badge-completed',
            ],
        ];
    }
}
