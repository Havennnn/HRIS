<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use PiaCore\Models\Admin;

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
            AttendanceSeeder::class,
            AttendanceLogSeeder::class,
            RequestSeeder::class,
            CareerSeeder::class,
            ApplicationSeeder::class,
            HolidaySeeder::class,
        ]);
    }
}

