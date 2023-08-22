<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        Subscription::create([
            'name' => 'basic-ease',
            'type' => 'core',
            'title' => 'Basic Ease 200',
            'description' => 'Enjoy essential features and tools with our "Basic Ease" subscription. Perfect for startups and small businesses, this plan provides access to core functionalities that streamline your operations. Stay organized, manage employees, and track time effortlessly. Upgrade your efficiency with the "Basic Ease" subscription.',
            'amount' => 200,
            'features' => json_encode([
                'Unlimited Salary Calculations',
                'Unlimited Taxable/Non-taxable Allowances',
                'Unlimited Taxable/Non-taxable Commissions',
                'Unlimited Deductions',
                'Customizable Salary Details',
                'Payslip Generation',
                'Leaves Tracking',
                'Government contributions',
                'Withheld Tax Computation',
                'Employee Management',
                'Reporting and Analytics',
                'No Minimum Employee Required',
            ])
        ]);

        Subscription::create([
            'name' => 'time-and-attendance',
            'type' => 'add-on',
            'title' => 'Time and Attendance 49',
            'description' => 'Elevate your workforce management with the "Time and Attendance 49" subscription. Unlock advanced time tracking and attendance features that empower your business to optimize productivity. Seamlessly manage employee schedules, monitor attendance trends, and ensure accurate payroll. Elevate your team\'s efficiency with the "Time and Attendance 49" subscription.',
            'amount' => 49,
            'features' => json_encode([
                'Core Functionalities',
                'Real-Time Attendance Tracking',
                'Unlimited Custom Shifts',
                'Flexible Time Scheduling',
                'Desktop as Terminal',
                'Mobile Phone as Terminal',
                'Tablet as Terminal',
                'Clock In/Out Anywhere',
                'Clock In/Out via QR',
                'Clock In/Out via Biometrics',
                'CSV Timesheet Integration',
                'Time-correction Requests',
                'Reporting and Analytics'
            ])
        ]);

        Subscription::create([
            'name' => 'auto-disburse',
            'type' => 'add-on',
            'title' => 'Auto Disbursement 49',
            'description' => "Simplify payroll processes and enhance financial efficiency with the 'Auto-Disburse' subscription. Say goodbye to manual disbursements and embrace automated salary payments. Seamlessly distribute salaries, benefits, and incentives to your employees with precision and ease. Enhance your payroll management with the streamlined 'Auto-Disburse' subscription.",
            'amount' => 49,
            'features' => json_encode([
                'Core Functionalities',
                'Bulk Disbursement',
                'Scheduled Disbursement',
                'via Instapay',
                'via Pesonet',
                'via G-Cash',
                'via Maya',
                'Transaction Monitoring',
                'Security and Compliance',
                'Reporting and Analytics'
            ])
        ]);
    }
}
