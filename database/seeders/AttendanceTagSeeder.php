<?php

namespace Database\Seeders;

use App\Enums\Status\AttendanceStatus;
use App\Models\AttendanceTag;
use Illuminate\Database\Seeder;

class AttendanceTagSeeder extends Seeder
{
    /**
     * Seed attendance tags from AttendanceStatus enum.
     */
    public function run(): void
    {
        foreach (AttendanceStatus::cases() as $status) {
            $tag = AttendanceTag::withTrashed()->firstOrNew([
                'value' => $status->value,
            ]);

            $tag->name = $status->label();
            $tag->deleted_at = null;
            $tag->save();
        }
    }
}
