<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call individual seeders in order
        $this->call([
            AdminSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            EmployeeSeeder::class,
            AttendanceTagSeeder::class,
            AttendanceSeeder::class,
            AttendanceLogSeeder::class,
            RequestSeeder::class,
            CareerSeeder::class,
            ApplicationSeeder::class,
            HolidaySeeder::class,
        ]);
    }
}

