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
        ]);

        // User::factory(10)->create();

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

        // Create 19 additional test admins (total 20)
        $testAdmins = [
            ['first_name' => 'John', 'last_name' => 'Smith', 'email' => 'john.smith@piacore.com'],
            ['first_name' => 'Emma', 'last_name' => 'Johnson', 'email' => 'emma.johnson@piacore.com'],
            ['first_name' => 'Michael', 'last_name' => 'Williams', 'email' => 'michael.williams@piacore.com'],
            ['first_name' => 'Olivia', 'last_name' => 'Brown', 'email' => 'olivia.brown@piacore.com'],
            ['first_name' => 'James', 'last_name' => 'Jones', 'email' => 'james.jones@piacore.com'],
            ['first_name' => 'Sophia', 'last_name' => 'Garcia', 'email' => 'sophia.garcia@piacore.com'],
            ['first_name' => 'William', 'last_name' => 'Miller', 'email' => 'william.miller@piacore.com'],
            ['first_name' => 'Ava', 'last_name' => 'Davis', 'email' => 'ava.davis@piacore.com'],
            ['first_name' => 'Benjamin', 'last_name' => 'Rodriguez', 'email' => 'benjamin.rodriguez@piacore.com'],
            ['first_name' => 'Isabella', 'last_name' => 'Martinez', 'email' => 'isabella.martinez@piacore.com'],
            ['first_name' => 'Lucas', 'last_name' => 'Hernandez', 'email' => 'lucas.hernandez@piacore.com'],
            ['first_name' => 'Mia', 'last_name' => 'Lopez', 'email' => 'mia.lopez@piacore.com'],
            ['first_name' => 'Henry', 'last_name' => 'Gonzalez', 'email' => 'henry.gonzalez@piacore.com'],
            ['first_name' => 'Charlotte', 'last_name' => 'Wilson', 'email' => 'charlotte.wilson@piacore.com'],
            ['first_name' => 'Alexander', 'last_name' => 'Anderson', 'email' => 'alexander.anderson@piacore.com'],
            ['first_name' => 'Amelia', 'last_name' => 'Thomas', 'email' => 'amelia.thomas@piacore.com'],
            ['first_name' => 'Daniel', 'last_name' => 'Taylor', 'email' => 'daniel.taylor@piacore.com'],
            ['first_name' => 'Evelyn', 'last_name' => 'Moore', 'email' => 'evelyn.moore@piacore.com'],
            ['first_name' => 'Matthew', 'last_name' => 'Jackson', 'email' => 'matthew.jackson@piacore.com'],
        ];

        foreach ($testAdmins as $index => $adminData) {
            Admin::query()->updateOrCreate([
                'email' => $adminData['email'],
            ], [
                'role' => null, // Unassigned role for testing variety
                'first_name' => $adminData['first_name'],
                'last_name' => $adminData['last_name'],
                'mobile_number' => sprintf('090000000%02d', $index + 2),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
