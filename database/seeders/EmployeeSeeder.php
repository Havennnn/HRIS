<?php

namespace Database\Seeders;

use App\Enums\Status\EmployeeStatus;
use App\Enums\Type\EmployeeType;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $positions = Position::all();
        
        $employees = [
            // IT Department
            ['first_name' => 'John', 'last_name' => 'Smith', 'position_id' => 1, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Sarah', 'last_name' => 'Johnson', 'position_id' => 2, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Michael', 'last_name' => 'Williams', 'position_id' => 3, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Emily', 'last_name' => 'Brown', 'position_id' => 4, 'type' => EmployeeType::PROBATIONARY, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'David', 'last_name' => 'Jones', 'position_id' => 5, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],

            // HR Department
            ['first_name' => 'Jennifer', 'last_name' => 'Garcia', 'position_id' => 12, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Robert', 'last_name' => 'Miller', 'position_id' => 13, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Lisa', 'last_name' => 'Davis', 'position_id' => 14, 'type' => EmployeeType::TRAINEE, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'James', 'last_name' => 'Rodriguez', 'position_id' => 15, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Amanda', 'last_name' => 'Martinez', 'position_id' => 11, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],

            // Finance Department
            ['first_name' => 'Christopher', 'last_name' => 'Hernandez', 'position_id' => 17, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Jessica', 'last_name' => 'Lopez', 'position_id' => 18, 'type' => EmployeeType::PROBATIONARY, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Daniel', 'last_name' => 'Gonzalez', 'position_id' => 19, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Ashley', 'last_name' => 'Wilson', 'position_id' => 20, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Matthew', 'last_name' => 'Anderson', 'position_id' => 16, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],

            // Marketing Department
            ['first_name' => 'Stephanie', 'last_name' => 'Thomas', 'position_id' => 22, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Andrew', 'last_name' => 'Taylor', 'position_id' => 23, 'type' => EmployeeType::TRAINEE, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Nicole', 'last_name' => 'Moore', 'position_id' => 24, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Joshua', 'last_name' => 'Jackson', 'position_id' => 25, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Lauren', 'last_name' => 'White', 'position_id' => 21, 'type' => EmployeeType::PROBATIONARY, 'status' => EmployeeStatus::ACTIVE],

            // Operations Department
            ['first_name' => 'Ryan', 'last_name' => 'Harris', 'position_id' => 27, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Melissa', 'last_name' => 'Martin', 'position_id' => 28, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Kevin', 'last_name' => 'Thompson', 'position_id' => 29, 'type' => EmployeeType::TRAINEE, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Kimberly', 'last_name' => 'Garcia', 'position_id' => 30, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Brandon', 'last_name' => 'Martinez', 'position_id' => 26, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],

            // Sales Department
            ['first_name' => 'Michelle', 'last_name' => 'Robinson', 'position_id' => 32, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Justin', 'last_name' => 'Clark', 'position_id' => 33, 'type' => EmployeeType::PROBATIONARY, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Samantha', 'last_name' => 'Lewis', 'position_id' => 34, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Brian', 'last_name' => 'Lee', 'position_id' => 35, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Rebecca', 'last_name' => 'Walker', 'position_id' => 31, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],

            // Customer Service Department
            ['first_name' => 'Eric', 'last_name' => 'Hall', 'position_id' => 37, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Angela', 'last_name' => 'Allen', 'position_id' => 38, 'type' => EmployeeType::TRAINEE, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Jason', 'last_name' => 'Young', 'position_id' => 39, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Heather', 'last_name' => 'King', 'position_id' => 40, 'type' => EmployeeType::PROBATIONARY, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Gregory', 'last_name' => 'Wright', 'position_id' => 36, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],

            // R&D Department
            ['first_name' => 'Catherine', 'last_name' => 'Scott', 'position_id' => 42, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Patrick', 'last_name' => 'Green', 'position_id' => 43, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Victoria', 'last_name' => 'Adams', 'position_id' => 44, 'type' => EmployeeType::TRAINEE, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Jonathan', 'last_name' => 'Baker', 'position_id' => 45, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Brittany', 'last_name' => 'Nelson', 'position_id' => 41, 'type' => EmployeeType::PROBATIONARY, 'status' => EmployeeStatus::ACTIVE],

            // Legal Department
            ['first_name' => 'Steven', 'last_name' => 'Hill', 'position_id' => 47, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Christina', 'last_name' => 'Campbell', 'position_id' => 48, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Timothy', 'last_name' => 'Mitchell', 'position_id' => 49, 'type' => EmployeeType::TRAINEE, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Cassandra', 'last_name' => 'Roberts', 'position_id' => 50, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Benjamin', 'last_name' => 'Carter', 'position_id' => 46, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],

            // Administration Department
            ['first_name' => 'Rachel', 'last_name' => 'Phillips', 'position_id' => 52, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Aaron', 'last_name' => 'Evans', 'position_id' => 53, 'type' => EmployeeType::TRAINEE, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Hannah', 'last_name' => 'Turner', 'position_id' => 54, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Tyler', 'last_name' => 'Parker', 'position_id' => 55, 'type' => EmployeeType::PROBATIONARY, 'status' => EmployeeStatus::ACTIVE],
            ['first_name' => 'Olivia', 'last_name' => 'Collins', 'position_id' => 51, 'type' => EmployeeType::REGULAR, 'status' => EmployeeStatus::ACTIVE],
        ];

        foreach ($employees as $employeeData) {
            $employeeData['email'] = strtolower($employeeData['first_name'] . '.' . $employeeData['last_name']) . '@company.com';
            $employeeData['middle_name'] = '';
            $employeeData['birthdate'] = rand(1980, 2000) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
            $employeeData['hired_date'] = now()->subMonths(rand(1, 36))->format('Y-m-d');
            $employeeData['mobile_number'] = '09' . rand(100000000, 999999999);

            Employee::query()->updateOrCreate(
                ['email' => $employeeData['email']],
                $employeeData
            );
        }
    }
}
