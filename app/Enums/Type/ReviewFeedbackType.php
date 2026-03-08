<?php

namespace App\Enums\Type;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum ReviewFeedbackType: int
{
    use HasMeta;
    use HasOptions;

    case STRENGTH = 1;
    case IMPROVEMENT = 2;
    case GENERAL = 3;

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::STRENGTH->value => [
                'label' => 'Strength',
            ],
            self::IMPROVEMENT->value => [
                'label' => 'Improvement',
            ],
            self::GENERAL->value => [
                'label' => 'General',
            ],
        ];
    }
}
