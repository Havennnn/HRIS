<?php

namespace App\Enums\Type;

use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum PayrollAdjustmentType: int
{
    use HasMeta;
    use HasOptions;

    case BONUS = 1;
    case DEDUCTION = 2;

    /**
     * {@inheritdoc}
     */
    protected static function metaMap(): array
    {
        return [
            self::BONUS->name => [
                'label' => 'Bonus',
                'variant' => 'badge-success',
            ],
            self::DEDUCTION->name => [
                'label' => 'Deduction',
                'variant' => 'badge-danger',
            ],
        ];
    }
}
