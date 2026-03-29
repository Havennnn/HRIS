<?php

namespace Database\Seeders;

use App\Constant\AdminRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use PiaCore\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's admin accounts.
     */
    public function run(): void
    {
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

        $roles = array_filter(
            AdminRole::cases(),
            fn (AdminRole $role): bool => $role !== AdminRole::SUPER_ADMIN,
        );

        $mobileNumber = 2;

        foreach ($roles as $role) {
            Admin::query()->updateOrCreate([
                'email' => sprintf('%s@gmail.com', $role->value),
            ], [
                'role' => $role,
                'first_name' => 'Test',
                'last_name' => 'Role',
                'mobile_number' => sprintf('090000000%02d', $mobileNumber),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            $mobileNumber++;
        }
    }
}
