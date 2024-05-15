<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Employee;
use App\Models\SalaryComputation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        $company = Company::inRandomOrder()->first();

        // Save the user and company relationship in the company_users pivot table
        CompanyUser::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        return [
            'user_id' => $user->id,
            'company_id' => $company->id,
            'company_employee_id' => $this->faker->randomNumber(6),
            'supervisor_user_id' => null,
            'department' => $this->faker->word,
            'job_title' => $this->faker->jobTitle,
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending']),
            'employment_status' => $this->faker->randomElement(['regular','probationary','terminated','separated']),
            'employment_type' => $this->faker->randomElement(['full-time','part-time','contract']),
            'mobile_number' => $this->faker->phoneNumber,
            'address_line' => $this->faker->address,
            'barangay_town_city_province' => $this->faker->city . ', ' . $this->faker->state . ', ' . $this->faker->country,
            'date_of_hire' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'date_of_termination' => null,
            'date_of_birth' => $this->faker->dateTimeBetween('-70 years', '-18 years'),
            'sss_number' => $this->faker->numerify('####-####-#'),
            'pagibig_number' => $this->faker->numerify('##-#####-##'),
            'philhealth_number' => $this->faker->numerify('#-####-####-##'),
            'tax_identification_number' => $this->faker->numerify('#########'),
            'bank_name' => $this->faker->randomElement(['Bank of America', 'Chase Bank', 'Wells Fargo']),
            'bank_account_name' => $this->faker->name,
            'bank_account_number' => $this->faker->bankAccountNumber,
            'profile_picture' => $this->faker->imageUrl(),
        ];
    }
}
