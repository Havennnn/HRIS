<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Position;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    public function run(): void
    {
        $positions = Position::all();

        $careerDescriptions = [
            // IT Department
            'Software Engineer' => 'We are looking for talented Software Engineers to join our development team. Experience with modern frameworks and cloud technologies preferred.',
            'Senior Software Engineer' => 'Seeking an experienced Senior Software Engineer to lead technical initiatives and mentor junior developers.',
            'IT Support Specialist' => 'IT Support Specialist needed to provide technical assistance and maintain our IT infrastructure.',
            'System Administrator' => 'Experienced System Administrator to manage and maintain our company servers and network infrastructure.',
            'DevOps Engineer' => 'Looking for a DevOps Engineer experienced in containerization, CI/CD pipelines, and cloud platforms.',
            'QA Engineer' => 'QA Engineer to ensure quality of our software products through comprehensive testing and automation.',
            'Technical Lead' => 'Technical Lead position for someone with strong leadership and deep technical expertise.',
            'IT Manager' => 'IT Manager to oversee the IT department and manage technology initiatives.',
            'Database Administrator' => 'Database Administrator to manage, optimize, and secure our database systems.',
            'Security Analyst' => 'Security Analyst to protect our systems from cyber threats and implement security measures.',

            // HR Department
            'HR Manager' => 'HR Manager to lead the human resources department and manage organizational talent.',
            'HR Specialist' => 'HR Specialist to handle recruitment, employee relations, and HR operations.',
            'Recruiter' => 'Recruiter to identify and attract top talent for various positions in our organization.',
            'Training Coordinator' => 'Training Coordinator to develop and deliver employee training programs.',
            'Benefits Administrator' => 'Benefits Administrator to manage employee benefits programs and compensation.',

            // Finance Department
            'Financial Analyst' => 'Financial Analyst to provide financial insights and support business decision-making.',
            'Accountant' => 'Accountant to manage financial records and ensure compliance with accounting standards.',
            'Finance Manager' => 'Finance Manager to oversee financial operations and strategy.',
            'Bookkeeper' => 'Bookkeeper to maintain accurate financial records and process transactions.',
            'Auditor' => 'Auditor to conduct internal and external audits and ensure financial compliance.',

            // Marketing Department
            'Marketing Manager' => 'Marketing Manager to develop and execute marketing strategies and campaigns.',
            'Marketing Specialist' => 'Marketing Specialist to support marketing initiatives and campaign execution.',
            'Content Writer' => 'Content Writer to create engaging content for various marketing channels.',
            'Social Media Manager' => 'Social Media Manager to manage our social media presence and engagement.',
            'SEO Specialist' => 'SEO Specialist to optimize our online presence and improve search engine rankings.',

            // Operations Department
            'Operations Manager' => 'Operations Manager to oversee operational efficiency and process improvement.',
            'Operations Coordinator' => 'Operations Coordinator to support daily operations and process management.',
            'Supply Chain Analyst' => 'Supply Chain Analyst to optimize supply chain processes and reduce costs.',
            'Logistics Coordinator' => 'Logistics Coordinator to manage inventory and distribution logistics.',
            'Procurement Officer' => 'Procurement Officer to manage vendor relationships and procurement operations.',

            // Sales Department
            'Sales Manager' => 'Sales Manager to lead the sales team and drive revenue growth.',
            'Sales Executive' => 'Sales Executive to develop client relationships and close sales deals.',
            'Sales Representative' => 'Sales Representative to promote products and services to potential customers.',
            'Account Manager' => 'Account Manager to maintain and grow customer accounts.',

            // Customer Service Department
            'Customer Service Manager' => 'Customer Service Manager to oversee customer support operations.',
            'Customer Service Representative' => 'Customer Service Representative to provide excellent support to our customers.',
            'Technical Support Specialist' => 'Technical Support Specialist to provide technical assistance to customers.',

            // Research and Development Department
            'Research Scientist' => 'Research Scientist to conduct research and develop innovative solutions.',
            'Product Developer' => 'Product Developer to design and develop new products.',
            'R&D Engineer' => 'R&D Engineer to support research initiatives and product development.',

            // Legal Department
            'Legal Counsel' => 'Legal Counsel to provide legal advice and ensure compliance.',
            'Compliance Officer' => 'Compliance Officer to ensure the company adheres to all relevant laws and regulations.',

            // Administration Department
            'Administrative Manager' => 'Administrative Manager to oversee administrative operations.',
            'Administrative Assistant' => 'Administrative Assistant to provide administrative support to the organization.',
            'Office Manager' => 'Office Manager to manage office operations and facilities.',
        ];

        foreach ($positions as $position) {
            $description = $careerDescriptions[$position->name] ?? 'Join our team as a ' . $position->name . '. We are looking for talented individuals to contribute to our organization\'s success.';

            Career::create([
                'position_id' => $position->id,
                'description' => $description,
                'is_active' => rand(0, 1) ? true : false,
            ]);
        }
    }
}
