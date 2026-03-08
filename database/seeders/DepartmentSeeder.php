<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Information Technology',
            'Human Resources',
            'Finance',
            'Marketing',
            'Operations',
            'Sales',
            'Customer Service',
            'Research and Development',
            'Legal',
            'Administration',
        ];

        foreach ($departments as $department) {
            Department::query()->updateOrCreate(
                ['name' => $department],
                ['name' => $department]
            );
        }
    }
}
