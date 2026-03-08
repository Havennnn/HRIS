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
            ['department_id' => $itDepartment?->id, 'name' => 'Software Engineer'],
            ['department_id' => $itDepartment?->id, 'name' => 'Senior Software Engineer'],
            ['department_id' => $itDepartment?->id, 'name' => 'IT Support Specialist'],
            ['department_id' => $itDepartment?->id, 'name' => 'System Administrator'],
            ['department_id' => $itDepartment?->id, 'name' => 'DevOps Engineer'],
            ['department_id' => $itDepartment?->id, 'name' => 'QA Engineer'],
            ['department_id' => $itDepartment?->id, 'name' => 'Technical Lead'],
            ['department_id' => $itDepartment?->id, 'name' => 'IT Manager'],
            ['department_id' => $itDepartment?->id, 'name' => 'Database Administrator'],
            ['department_id' => $itDepartment?->id, 'name' => 'Security Analyst'],

            // HR Department
            ['department_id' => $hrDepartment?->id, 'name' => 'HR Manager'],
            ['department_id' => $hrDepartment?->id, 'name' => 'HR Specialist'],
            ['department_id' => $hrDepartment?->id, 'name' => 'Recruiter'],
            ['department_id' => $hrDepartment?->id, 'name' => 'Training Coordinator'],
            ['department_id' => $hrDepartment?->id, 'name' => 'Benefits Administrator'],

            // Finance Department
            ['department_id' => $financeDepartment?->id, 'name' => 'Financial Analyst'],
            ['department_id' => $financeDepartment?->id, 'name' => 'Accountant'],
            ['department_id' => $financeDepartment?->id, 'name' => 'Finance Manager'],
            ['department_id' => $financeDepartment?->id, 'name' => 'Bookkeeper'],
            ['department_id' => $financeDepartment?->id, 'name' => 'Auditor'],

            // Marketing Department
            ['department_id' => $marketingDepartment?->id, 'name' => 'Marketing Manager'],
            ['department_id' => $marketingDepartment?->id, 'name' => 'Marketing Specialist'],
            ['department_id' => $marketingDepartment?->id, 'name' => 'Content Writer'],
            ['department_id' => $marketingDepartment?->id, 'name' => 'Social Media Manager'],
            ['department_id' => $marketingDepartment?->id, 'name' => 'SEO Specialist'],

            // Operations Department
            ['department_id' => $operationsDepartment?->id, 'name' => 'Operations Manager'],
            ['department_id' => $operationsDepartment?->id, 'name' => 'Operations Coordinator'],
            ['department_id' => $operationsDepartment?->id, 'name' => 'Supply Chain Analyst'],
            ['department_id' => $operationsDepartment?->id, 'name' => 'Logistics Coordinator'],
            ['department_id' => $operationsDepartment?->id, 'name' => 'Warehouse Supervisor'],

            // Sales Department
            ['department_id' => $salesDepartment?->id, 'name' => 'Sales Manager'],
            ['department_id' => $salesDepartment?->id, 'name' => 'Sales Representative'],
            ['department_id' => $salesDepartment?->id, 'name' => 'Account Executive'],
            ['department_id' => $salesDepartment?->id, 'name' => 'Business Development Manager'],
            ['department_id' => $salesDepartment?->id, 'name' => 'Sales Analyst'],

            // Customer Service Department
            ['department_id' => $customerServiceDepartment?->id, 'name' => 'Customer Service Manager'],
            ['department_id' => $customerServiceDepartment?->id, 'name' => 'Customer Support Representative'],
            ['department_id' => $customerServiceDepartment?->id, 'name' => 'Technical Support Engineer'],
            ['department_id' => $customerServiceDepartment?->id, 'name' => 'Customer Success Manager'],
            ['department_id' => $customerServiceDepartment?->id, 'name' => 'Call Center Supervisor'],

            // R&D Department
            ['department_id' => $rdDepartment?->id, 'name' => 'Research Scientist'],
            ['department_id' => $rdDepartment?->id, 'name' => 'Product Designer'],
            ['department_id' => $rdDepartment?->id, 'name' => 'UX Researcher'],
            ['department_id' => $rdDepartment?->id, 'name' => 'Innovation Manager'],
            ['department_id' => $rdDepartment?->id, 'name' => 'Data Scientist'],

            // Legal Department
            ['department_id' => $legalDepartment?->id, 'name' => 'Legal Counsel'],
            ['department_id' => $legalDepartment?->id, 'name' => 'Paralegal'],
            ['department_id' => $legalDepartment?->id, 'name' => 'Compliance Officer'],
            ['department_id' => $legalDepartment?->id, 'name' => 'Contract Manager'],
            ['department_id' => $legalDepartment?->id, 'name' => 'Legal Assistant'],

            // Administration Department
            ['department_id' => $adminDepartment?->id, 'name' => 'Administrative Assistant'],
            ['department_id' => $adminDepartment?->id, 'name' => 'Office Manager'],
            ['department_id' => $adminDepartment?->id, 'name' => 'Receptionist'],
            ['department_id' => $adminDepartment?->id, 'name' => 'Executive Secretary'],
            ['department_id' => $adminDepartment?->id, 'name' => 'Facilities Coordinator'],
        ];

        foreach ($positions as $position) {
            if ($position['department_id']) {
                Position::query()->updateOrCreate(
                    ['name' => $position['name'], 'department_id' => $position['department_id']],
                    $position
                );
            }
        }
    }
}
