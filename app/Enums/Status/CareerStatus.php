<?php

namespace App\Enums\Status;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum CareerStatus: int
{
    use HasMeta;
    use HasOptions;

    case DRAFT = 0;
    case PUBLISHED = 1;

    protected static function metaMap(): array
    {
        return [
            self::DRAFT->value => [
                'label' => 'Draft',
                'variant' => 'badge-draft',
            ],
            self::PUBLISHED->value => [
                'label' => 'Published',
                'variant' => 'badge-active',
            ],
        ];
    }
}
