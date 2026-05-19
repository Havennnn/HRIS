<?php

namespace Database\Seeders;

use App\Enums\Type\HolidayType;
use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Seed Philippine public holidays for 2026.
     * Sources: Proclamation No. 368 (s. 2025) and regular holidays under RA 9492.
     */
    public function run(): void
    {
        Holiday::where('date', '>=', '2026-01-01')
            ->where('date', '<=', '2026-12-31')
            ->delete();

        $holidays = [
            // ── Regular Holidays ──────────────────────────────────────────────
            [
                'name' => "New Year's Day",
                'date' => '2026-01-01',
                'type' => HolidayType::REGULAR->value,
                'description' => 'First day of the year. Regular holiday under RA 9492.',
            ],
            [
                'name' => 'Araw ng Kagitingan (Day of Valor)',
                'date' => '2026-04-09',
                'type' => HolidayType::REGULAR->value,
                'description' => 'Commemorates the Fall of Bataan. Regular holiday under RA 9492.',
            ],
            [
                'name' => 'Maundy Thursday',
                'date' => '2026-04-02',
                'type' => HolidayType::REGULAR->value,
                'description' => 'Holy Week. Date varies yearly based on the lunar calendar.',
            ],
            [
                'name' => 'Good Friday',
                'date' => '2026-04-03',
                'type' => HolidayType::REGULAR->value,
                'description' => 'Holy Week. Date varies yearly based on the lunar calendar.',
            ],
            [
                'name' => 'Labor Day',
                'date' => '2026-05-01',
                'type' => HolidayType::REGULAR->value,
                'description' => "International Workers' Day. Regular holiday under RA 9492.",
            ],
            [
                'name' => 'Eid\'l Fitr (End of Ramadan)',
                'date' => '2026-03-20',
                'type' => HolidayType::REGULAR->value,
                'description' => 'Marks the end of Ramadan. Exact date subject to lunar observation.',
            ],
            [
                'name' => 'Independence Day',
                'date' => '2026-06-12',
                'type' => HolidayType::REGULAR->value,
                'description' => 'Philippine Declaration of Independence from Spain in 1898.',
            ],
            [
                'name' => "National Heroes' Day",
                'date' => '2026-08-31',
                'type' => HolidayType::REGULAR->value,
                'description' => 'Last Monday of August. Honors all Philippine national heroes.',
            ],
            [
                'name' => "Eid'l Adha (Feast of Sacrifice)",
                'date' => '2026-05-27',
                'type' => HolidayType::REGULAR->value,
                'description' => 'Islamic holiday. Exact date subject to lunar observation.',
            ],
            [
                'name' => 'Bonifacio Day',
                'date' => '2026-11-30',
                'type' => HolidayType::REGULAR->value,
                'description' => 'Birthday of Andres Bonifacio, Father of the Philippine Revolution.',
            ],
            [
                'name' => 'Christmas Day',
                'date' => '2026-12-25',
                'type' => HolidayType::REGULAR->value,
                'description' => 'Celebration of the birth of Jesus Christ.',
            ],
            [
                'name' => 'Rizal Day',
                'date' => '2026-12-30',
                'type' => HolidayType::REGULAR->value,
                'description' => 'Commemorates the martyrdom of Dr. José Rizal in 1896.',
            ],

            // ── Special Non-Working Holidays ─────────────────────────────────
            [
                'name' => "New Year's Eve",
                'date' => '2026-12-31',
                'type' => HolidayType::SPECIAL->value,
                'description' => 'Special non-working holiday declared by proclamation.',
            ],
            [
                'name' => 'Chinese New Year',
                'date' => '2026-02-17',
                'type' => HolidayType::SPECIAL->value,
                'description' => 'Year of the Horse. Special non-working holiday per proclamation.',
            ],
            [
                'name' => 'Black Saturday',
                'date' => '2026-04-04',
                'type' => HolidayType::SPECIAL->value,
                'description' => 'Holy Week. Special non-working holiday.',
            ],
            [
                'name' => 'Ninoy Aquino Day',
                'date' => '2026-08-21',
                'type' => HolidayType::SPECIAL->value,
                'description' => "Commemorates the assassination of Senator Benigno 'Ninoy' Aquino Jr.",
            ],
            [
                'name' => 'All Saints Day',
                'date' => '2026-11-01',
                'type' => HolidayType::SPECIAL->value,
                'description' => 'Day to honor all saints. Special non-working holiday.',
            ],
            [
                'name' => 'All Souls Day',
                'date' => '2026-11-02',
                'type' => HolidayType::SPECIAL->value,
                'description' => 'Day of prayer for the souls of the faithful departed.',
            ],
            [
                'name' => 'Feast of the Immaculate Conception',
                'date' => '2026-12-08',
                'type' => HolidayType::SPECIAL->value,
                'description' => 'Catholic feast day. Special non-working holiday.',
            ],
            [
                'name' => 'Christmas Eve',
                'date' => '2026-12-24',
                'type' => HolidayType::SPECIAL->value,
                'description' => 'Day before Christmas. Special non-working holiday.',
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(
                ['date' => $holiday['date'], 'name' => $holiday['name']],
                $holiday
            );
        }

        $this->command->info('✅ Seeded '.count($holidays).' Philippine holidays for 2026.');
    }
}
