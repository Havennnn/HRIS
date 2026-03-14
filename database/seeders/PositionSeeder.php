<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $itDepartment = Department::where('name', 'Information Technology')->first();
        $hrDepartment = Department::where('name', 'Human Resources')->first();
        $financeDepartment = Department::where('name', 'Finance')->first();
        $marketingDepartment = Department::where('name', 'Marketing')->first();
        $operationsDepartment = Department::where('name', 'Operations')->first();
        $salesDepartment = Department::where('name', 'Sales')->first();
        $customerServiceDepartment = Department::where('name', 'Customer Service')->first();
        $rdDepartment = Department::where('name', 'Research and Development')->first();
        $legalDepartment = Department::where('name', 'Legal')->first();
        $adminDepartment = Department::where('name', 'Administration')->first();

        $positions = [
            // IT Department
            [
                'department_id' => $itDepartment?->id,
                'name' => 'Software Engineer',
                'level' => 'Junior',
                'salary' => 35000,
                'allowance' => 5000,
            ],
            [
                'department_id' => $itDepartment?->id,
                'name' => 'Software Engineer',
                'level' => 'Mid-Level',
                'salary' => 50000,
                'allowance' => 7000,
            ],
            [
                'department_id' => $itDepartment?->id,
                'name' => 'Software Engineer',
                'level' => 'Senior',
                'salary' => 75000,
                'allowance' => 10000,
            ],
            [
                'department_id' => $itDepartment?->id,
                'name' => 'Senior Software Engineer',
                'level' => 'Senior',
                'salary' => 85000,
                'allowance' => 12000,
            ],
            [
                'department_id' => $itDepartment?->id,
                'name' => 'IT Support Specialist',
                'level' => 'Junior',
                'salary' => 25000,
                'allowance' => 3000,
            ],
            [
                'department_id' => $itDepartment?->id,
                'name' => 'System Administrator',
                'level' => 'Mid-Level',
                'salary' => 55000,
                'allowance' => 8000,
            ],
            [
                'department_id' => $itDepartment?->id,
                'name' => 'DevOps Engineer',
                'level' => 'Senior',
                'salary' => 80000,
                'allowance' => 11000,
            ],
            [
                'department_id' => $itDepartment?->id,
                'name' => 'QA Engineer',
                'level' => 'Junior',
                'salary' => 30000,
                'allowance' => 4000,
            ],
            [
                'department_id' => $itDepartment?->id,
                'name' => 'Technical Lead',
                'level' => 'Lead',
                'salary' => 100000,
                'allowance' => 15000,
            ],
            [
                'department_id' => $itDepartment?->id,
                'name' => 'IT Manager',
                'level' => 'Manager',
                'salary' => 120000,
                'allowance' => 20000,
            ],
            [
                'department_id' => $itDepartment?->id,
                'name' => 'Database Administrator',
                'level' => 'Senior',
                'salary' => 78000,
                'allowance' => 10500,
            ],
            [
                'department_id' => $itDepartment?->id,
                'name' => 'Security Analyst',
                'level' => 'Senior',
                'salary' => 82000,
                'allowance' => 11500,
            ],

            // HR Department
            [
                'department_id' => $hrDepartment?->id,
                'name' => 'HR Manager',
                'level' => 'Manager',
                'salary' => 90000,
                'allowance' => 15000,
            ],
            [
                'department_id' => $hrDepartment?->id,
                'name' => 'HR Specialist',
                'level' => 'Mid-Level',
                'salary' => 40000,
                'allowance' => 6000,
            ],
            [
                'department_id' => $hrDepartment?->id,
                'name' => 'Recruiter',
                'level' => 'Junior',
                'salary' => 28000,
                'allowance' => 4000,
            ],
            [
                'department_id' => $hrDepartment?->id,
                'name' => 'Training Coordinator',
                'level' => 'Junior',
                'salary' => 26000,
                'allowance' => 3500,
            ],
            [
                'department_id' => $hrDepartment?->id,
                'name' => 'Benefits Administrator',
                'level' => 'Mid-Level',
                'salary' => 42000,
                'allowance' => 6500,
            ],

            // Finance Department
            [
                'department_id' => $financeDepartment?->id,
                'name' => 'Financial Analyst',
                'level' => 'Mid-Level',
                'salary' => 50000,
                'allowance' => 7500,
            ],
            [
                'department_id' => $financeDepartment?->id,
                'name' => 'Accountant',
                'level' => 'Mid-Level',
                'salary' => 45000,
                'allowance' => 6500,
            ],
            [
                'department_id' => $financeDepartment?->id,
                'name' => 'Finance Manager',
                'level' => 'Manager',
                'salary' => 95000,
                'allowance' => 16000,
            ],
            [
                'department_id' => $financeDepartment?->id,
                'name' => 'Bookkeeper',
                'level' => 'Junior',
                'salary' => 22000,
                'allowance' => 3000,
            ],
            [
                'department_id' => $financeDepartment?->id,
                'name' => 'Auditor',
                'level' => 'Senior',
                'salary' => 70000,
                'allowance' => 10000,
            ],

            // Marketing Department
            [
                'department_id' => $marketingDepartment?->id,
                'name' => 'Marketing Manager',
                'level' => 'Manager',
                'salary' => 85000,
                'allowance' => 13000,
            ],
            [
                'department_id' => $marketingDepartment?->id,
                'name' => 'Marketing Specialist',
                'level' => 'Mid-Level',
                'salary' => 40000,
                'allowance' => 5500,
            ],
            [
                'department_id' => $marketingDepartment?->id,
                'name' => 'Content Writer',
                'level' => 'Junior',
                'salary' => 25000,
                'allowance' => 3500,
            ],
            [
                'department_id' => $marketingDepartment?->id,
                'name' => 'Social Media Manager',
                'level' => 'Mid-Level',
                'salary' => 45000,
                'allowance' => 6000,
            ],
            [
                'department_id' => $marketingDepartment?->id,
                'name' => 'SEO Specialist',
                'level' => 'Mid-Level',
                'salary' => 42000,
                'allowance' => 5500,
            ],

            // Operations Department
            [
                'department_id' => $operationsDepartment?->id,
                'name' => 'Operations Manager',
                'level' => 'Manager',
                'salary' => 80000,
                'allowance' => 12000,
            ],
            [
                'department_id' => $operationsDepartment?->id,
                'name' => 'Operations Coordinator',
                'level' => 'Junior',
                'salary' => 28000,
                'allowance' => 4000,
            ],
            [
                'department_id' => $operationsDepartment?->id,
                'name' => 'Supply Chain Analyst',
                'level' => 'Mid-Level',
                'salary' => 42000,
                'allowance' => 6000,
            ],
            [
                'department_id' => $operationsDepartment?->id,
                'name' => 'Logistics Coordinator',
                'level' => 'Junior',
                'salary' => 26000,
                'allowance' => 3500,
            ],
            [
                'department_id' => $operationsDepartment?->id,
                'name' => 'Warehouse Supervisor',
                'level' => 'Mid-Level',
                'salary' => 38000,
                'allowance' => 5000,
            ],

            // Sales Department
            [
                'department_id' => $salesDepartment?->id,
                'name' => 'Sales Manager',
                'level' => 'Manager',
                'salary' => 90000,
                'allowance' => 15000,
            ],
            [
                'department_id' => $salesDepartment?->id,
                'name' => 'Sales Representative',
                'level' => 'Junior',
                'salary' => 24000,
                'allowance' => 5000,
            ],
            [
                'department_id' => $salesDepartment?->id,
                'name' => 'Account Executive',
                'level' => 'Mid-Level',
                'salary' => 48000,
                'allowance' => 8000,
            ],
            [
                'department_id' => $salesDepartment?->id,
                'name' => 'Business Development Manager',
                'level' => 'Senior',
                'salary' => 95000,
                'allowance' => 18000,
            ],
            [
                'department_id' => $salesDepartment?->id,
                'name' => 'Sales Analyst',
                'level' => 'Mid-Level',
                'salary' => 44000,
                'allowance' => 6000,
            ],

            // Customer Service Department
            [
                'department_id' => $customerServiceDepartment?->id,
                'name' => 'Customer Service Manager',
                'level' => 'Manager',
                'salary' => 65000,
                'allowance' => 10000,
            ],
            [
                'department_id' => $customerServiceDepartment?->id,
                'name' => 'Customer Support Representative',
                'level' => 'Junior',
                'salary' => 20000,
                'allowance' => 2500,
            ],
            [
                'department_id' => $customerServiceDepartment?->id,
                'name' => 'Technical Support Engineer',
                'level' => 'Mid-Level',
                'salary' => 40000,
                'allowance' => 5500,
            ],
            [
                'department_id' => $customerServiceDepartment?->id,
                'name' => 'Customer Success Manager',
                'level' => 'Senior',
                'salary' => 70000,
                'allowance' => 10000,
            ],
            [
                'department_id' => $customerServiceDepartment?->id,
                'name' => 'Call Center Supervisor',
                'level' => 'Mid-Level',
                'salary' => 38000,
                'allowance' => 5000,
            ],

            // R&D Department
            [
                'department_id' => $rdDepartment?->id,
                'name' => 'Research Scientist',
                'level' => 'Senior',
                'salary' => 85000,
                'allowance' => 12000,
            ],
            [
                'department_id' => $rdDepartment?->id,
                'name' => 'Product Designer',
                'level' => 'Mid-Level',
                'salary' => 55000,
                'allowance' => 8000,
            ],
            [
                'department_id' => $rdDepartment?->id,
                'name' => 'UX Researcher',
                'level' => 'Mid-Level',
                'salary' => 52000,
                'allowance' => 7500,
            ],
            [
                'department_id' => $rdDepartment?->id,
                'name' => 'Innovation Manager',
                'level' => 'Manager',
                'salary' => 110000,
                'allowance' => 18000,
            ],
            [
                'department_id' => $rdDepartment?->id,
                'name' => 'Data Scientist',
                'level' => 'Senior',
                'salary' => 90000,
                'allowance' => 13000,
            ],

            // Legal Department
            [
                'department_id' => $legalDepartment?->id,
                'name' => 'Legal Counsel',
                'level' => 'Senior',
                'salary' => 100000,
                'allowance' => 16000,
            ],
            [
                'department_id' => $legalDepartment?->id,
                'name' => 'Paralegal',
                'level' => 'Junior',
                'salary' => 28000,
                'allowance' => 4000,
            ],
            [
                'department_id' => $legalDepartment?->id,
                'name' => 'Compliance Officer',
                'level' => 'Mid-Level',
                'salary' => 55000,
                'allowance' => 8000,
            ],
            [
                'department_id' => $legalDepartment?->id,
                'name' => 'Contract Manager',
                'level' => 'Senior',
                'salary' => 75000,
                'allowance' => 11000,
            ],
            [
                'department_id' => $legalDepartment?->id,
                'name' => 'Legal Assistant',
                'level' => 'Junior',
                'salary' => 22000,
                'allowance' => 3000,
            ],

            // Administration Department
            [
                'department_id' => $adminDepartment?->id,
                'name' => 'Administrative Assistant',
                'level' => 'Junior',
                'salary' => 20000,
                'allowance' => 2500,
            ],
            [
                'department_id' => $adminDepartment?->id,
                'name' => 'Office Manager',
                'level' => 'Manager',
                'salary' => 60000,
                'allowance' => 9000,
            ],
            [
                'department_id' => $adminDepartment?->id,
                'name' => 'Receptionist',
                'level' => 'Junior',
                'salary' => 18000,
                'allowance' => 2000,
            ],
            [
                'department_id' => $adminDepartment?->id,
                'name' => 'Executive Secretary',
                'level' => 'Mid-Level',
                'salary' => 40000,
                'allowance' => 6000,
            ],
            [
                'department_id' => $adminDepartment?->id,
                'name' => 'Facilities Coordinator',
                'level' => 'Junior',
                'salary' => 24000,
                'allowance' => 3500,
            ],
        ];

        foreach ($positions as $position) {
            if ($position['department_id']) {
                Position::query()->updateOrCreate(
                    [
                        'name' => $position['name'],
                        'level' => $position['level'],
                        'department_id' => $position['department_id'],
                    ],
                    $position
                );
            }
        }
    }
}
