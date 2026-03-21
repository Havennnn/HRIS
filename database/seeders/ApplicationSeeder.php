<?php

namespace Database\Seeders;

use App\Enums\Status\ApplicationStatus;
use App\Models\Application;
use App\Models\Career;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $careers = Career::all();

        if ($careers->isEmpty()) {
            return;
        }

        $firstNames = [
            'John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'James', 'Amanda',
            'Christopher', 'Jessica', 'Daniel', 'Ashley', 'Matthew', 'Nicole', 'Andrew', 'Lauren', 'Joshua', 'Kimberly',
            'Ryan', 'Melissa', 'Kevin', 'Rachel', 'Brian', 'Jennifer', 'Jason', 'Stephanie', 'Jeff', 'Amy',
            'Carlos', 'Maria', 'Juan', 'Rosa', 'Pedro', 'Ana', 'Luis', 'Sofia', 'Miguel', 'Isabella'
        ];

        $lastNames = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
            'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin',
            'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson'
        ];

        for ($i = 0; $i < 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $career = $careers->random();

            $birthdate = now()->subYears(rand(18, 50))->subDays(rand(0, 365));
            $mobileNumber = '+63' . str_pad(rand(9000000000, 9999999999), 10, '0', STR_PAD_LEFT);

            $application = Application::create([
                'career_id' => $career->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => rand(0, 1) ? $firstNames[array_rand($firstNames)] : null,
                'birthdate' => $birthdate,
                'mobile_number' => $mobileNumber,
                'email' => strtolower($firstName . '.' . $lastName . rand(1, 9999)) . '@example.com',
                'status' => $this->getRandomStatus(),
            ]);

            // Create application details with resume and portfolio (optional)
            $application->details()->create([
                'resume_id' => null,
                'portfolio_id' => null,
            ]);

            // Create application interview for some applications
            if (rand(0, 1)) {
                $scheduledDate = now()->subDays(rand(1, 60))->setTime(rand(8, 16), [0, 30][rand(0, 1)]);
                $application->interview()->create([
                    'scheduled_at' => $scheduledDate,
                    'location' => $this->getRandomVenue(),
                    'interviewer_name' => $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)],
                    'score' => rand(0, 1) ? rand(50, 100) : null,
                    'feedback' => rand(0, 1) ? $this->getRandomFeedback() : null,
                ]);
            }
        }
    }

    private function getRandomStatus(): ApplicationStatus
    {
        $statuses = [
            ApplicationStatus::PENDING,
            ApplicationStatus::REVIEWING,
            ApplicationStatus::INTERVIEW,
            ApplicationStatus::HIRED,
            ApplicationStatus::REJECTED,
        ];

        return $statuses[array_rand($statuses)];
    }

    private function getRandomVenue(): string
    {
        $venues = [
            'Main Office - Conference Room A',
            'Main Office - Conference Room B',
            'Video Conference',
            'Coffee Shop',
            'Co-working Space',
            'Company Campus',
            'Remote Interview',
        ];

        return $venues[array_rand($venues)];
    }

    private function getRandomFeedback(): string
    {
        $feedbacks = [
            'Excellent communication skills and problem-solving abilities. Strong cultural fit for the team.',
            'Good technical knowledge. Needs more experience in specific domain areas.',
            'Great team player with strong leadership potential. Highly recommended for hiring.',
            'Promising candidate with relevant experience. Minor skill gaps can be addressed through training.',
            'Outstanding performance in technical assessments. Exceeds expectations for the role.',
            'Good interview performance. Strong background and relevant certifications. Ready to start immediately.',
            'Satisfactory performance overall. Some concerns regarding hands-on experience levels.',
            'Impressive portfolio and project history. Would be a great addition to the team.',
        ];

        return $feedbacks[array_rand($feedbacks)];
    }
}
