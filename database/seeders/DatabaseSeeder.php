<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use PiaCore\Enums\AdminRole;
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
            DepartmentSeeder::class,
            PositionSeeder::class,
            EmployeeSeeder::class,
            AttendanceSeeder::class,
            AttendanceLogSeeder::class,
            RequestSeeder::class,

            // System Setup
            HolidaySeeder::class,
        ]);

        // Super Admin
        Admin::query()->updateOrCreate([
            'email' => 'latsmarbls@piacore.com',
        ], [
            'role' => AdminRole::SUPER_ADMIN,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'mobile_number' => '09000000001',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
